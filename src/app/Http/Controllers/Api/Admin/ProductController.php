<?php

namespace VCComponent\Laravel\Product\Http\Controllers\Api\Admin;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use VCComponent\Laravel\Product\Entities\UserProduct;
use VCComponent\Laravel\Product\Events\ProductCreatedByAdminEvent;
use VCComponent\Laravel\Product\Events\ProductDeletedEvent;
use VCComponent\Laravel\Product\Events\ProductStockChangedByAdminEvent;
use VCComponent\Laravel\Product\Events\ProductUpdatedByAdminEvent;
use VCComponent\Laravel\Product\Exports\ProductExports;
use VCComponent\Laravel\Product\Repositories\ProductRepository;
use VCComponent\Laravel\Product\Traits\Helpers;
use VCComponent\Laravel\Product\Transformers\ProductTransformer;
use VCComponent\Laravel\Product\Validators\ProductValidator;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;
use VCComponent\Laravel\Vicoders\Core\Exceptions\NotFoundException;
use VCComponent\Laravel\Vicoders\Core\Exceptions\PermissionDeniedException;

class ProductController extends ApiController
{
    use Helpers;

    public function __construct(ProductRepository $repository, ProductValidator $validator, ProductExports $exports)
    {
        $this->repository = $repository;
        $this->entity     = $repository->getEntity();
        $this->validator  = $validator;
        $this->exports    = $exports;

        if (config('product.auth_middleware.admin.middleware') !== '') {
            $this->middleware(
                config('product.auth_middleware.admin.middleware'),
                ['except' => config('product.auth_middleware.admin.except')]
            );
        }

        if (isset(config('product.transformers')['product'])) {
            $this->transformer = config('product.transformers.product');
        } else {
            $this->transformer = ProductTransformer::class;
        }
    }

    public function index(Request $request)
    {

        $query = $this->entity;

        $query = $this->getFromDate($request, $query);
        $query = $this->getToDate($request, $query);
        $query = $this->getStatus($request, $query);
        $query = $this->getStocks($request, $query);

        $query = $this->filterAuthor($request, $query);

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name', 'description', 'price'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $per_page = $request->has('per_page') ? (int) $request->get('per_page') : 15;
        $products = $query->paginate($per_page);

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }
        return $this->response->paginator($products, $transformer);
    }

    function list(Request $request) {
        $query = $this->entity;

        $query = $this->getFromDate($request, $query);
        $query = $this->getToDate($request, $query);
        $query = $this->getStock($request, $query);
        $query = $this->getStatus($request, $query);

        $query = $this->filterAuthor($request, $query);

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name', 'description', 'price'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $products = $query->get();

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->collection($products, $transformer);
    }

    public function show(Request $request, $id)
    {
        $product = $this->repository->find($id);
        if (!$product) {
            throw new NotFoundException('Product');
        }

        if (config('product.auth_middleware.admin.middleware') !== '') {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToShow($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->item($product, $transformer);
    }

    public function store(Request $request)
    {

        if (config('product.auth_middleware.admin.middleware') !== '') {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToCreate($user)) {
                throw new PermissionDeniedException();
            }
        }

        $data = $this->filterProductRequestData($request, $this->entity);

        $schema_rules   = $this->validator->getSchemaRules($this->entity);
        $no_rule_fields = $this->validator->getNoRuleFields($this->entity);

        if ($request->get('random_sku', false)) {

            $sku       = $this->getSku();
            $check_sku = $this->repository->checkSku($sku);
            if (!$check_sku) {
                $sku = $this->getSku();
            }
            $data['default']['sku'] = $sku;
        }

        $this->validator->isValid($data['default'], 'RULE_ADMIN_CREATE');
        $this->validator->isSchemaValid($data['schema'], $schema_rules);

        $product = $this->repository->create($data['default']);
        $product->save();

        if (count($data['schema'])) {
            foreach ($data['schema'] as $key => $value) {
                $product->productMetas()->create([
                    'key'   => $key,
                    'value' => $value,
                ]);
            }
        }

        if (count($no_rule_fields)) {
            foreach ($no_rule_fields as $key => $value) {
                $product->productMetas()->updateOrCreate([
                    'key'   => $key,
                    'value' => null,
                ], ['value' => '']);
            }
        }

        event(new ProductCreatedByAdminEvent($product));

        return $this->response->item($product, new $this->transformer);
    }

    public function update(Request $request, $id)
    {
        $product = $this->entity->find($id);
        if (!$product) {
            throw new NotFoundException('Product');
        }

        if (config('product.auth_middleware.admin.middleware') !== '') {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToUpdateItem($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        $data         = $this->filterProductRequestData($request, $this->entity);
        $schema_rules = $this->validator->getSchemaRules($this->entity);

        $this->validator->isValid($data['default'], 'RULE_ADMIN_UPDATE');
        $this->validator->isSchemaValid($data['schema'], $schema_rules);

        $product = $this->repository->update($data['default'], $id);

        if ($request->has('status')) {
            $product->status = $request->get('status');
            $product->save();
        }

        if (count($data['schema'])) {
            foreach ($data['schema'] as $key => $value) {
                $product->productMetas()->updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        event(new ProductUpdatedByAdminEvent($product));

        return $this->response->item($product, new $this->transformer);
    }

    public function destroy(Request $request, $id)
    {
        $product = $this->entity->find($id);
        if (!$product) {
            throw new NotFoundException('Product');
        }

        if (config('product.auth_middleware.admin.middleware') !== '') {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToDelete($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        $this->repository->delete($id);

        event(new ProductDeletedEvent($product));

        return $this->success();
    }

    public function bulkUpdateStatus(Request $request)
    {
        if (config('product.auth_middleware.admin.middleware') !== '') {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToUpdate($user)) {
                throw new PermissionDeniedException();
            }
        }

        $this->validator->isValid($request, 'BULK_UPDATE_STATUS');
        $this->repository->bulkUpdateStatus($request);

        return $this->success();
    }

    public function updateStatusItem(Request $request, $id)
    {
        if (config('product.auth_middleware.admin.middleware') !== '') {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToUpdateItem($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        $product = $this->entity->find($id);
        if (!$product) {
            throw new NotFoundException('Product');
        }

        $this->validator->isValid($request, 'UPDATE_STATUS_ITEM');

        $data            = $request->all();
        $product->status = $data['status'];
        $product->save();

        return $this->success();
    }

    public function exportExcel(Request $request)
    {
        $products = $this->entity;

        $products = $this->getFromDate($request, $products);
        $products = $this->getToDate($request, $products);
        $products = $this->getStatus($request, $products);
        $products = $products->get();

        Excel::store(new $this->exports($products), 'products.xlsx', 'excel');

        return Response()->download(public_path('exports/products.xlsx'));
    }

    public function changeDatetime(Request $request, $id)
    {
        $product = $this->repository->where('id', $id)->first();

        if (!$product) {
            throw new NotFoundException('Product');
        }

        if (config('product.auth_middleware.admin.middleware') !== '') {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToUpdateItem($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        $this->validator->isValid($request, 'RULE_ADMIN_UPDATE_DATE');

        $data = $request->all();

        $data = Carbon::parse($request->published_date)->format('Y-m-d');

        $product->published_date = $data;
        $product->save();

        return $this->response->item($product, new $this->transformer);
    }

    public function checkStock($id)
    {
        $product = $this->repository->find($id);

        if (!$product) {
            throw new NotFoundException('Product');
        }

        if ($product->quantity == 0) {
            return response()->json(['in_stock' => false]);
        }

        return response()->json(['in_stock' => true]);
    }

    public function changeQuantity(Request $request, $id)
    {
        $product = $this->repository->find($id);

        if (!$product) {
            throw new NotFoundException('Product');
        }

        $request->validate([
            'quantity' => ['required', 'numeric'],
        ]);

        $product->update([
            'quantity' => $request['quantity'],
        ]);

        event(new ProductStockChangedByAdminEvent($product));

        return $product;
    }

    public function updateQuantity(Request $request, $id)
    {
        $product = $this->repository->find($id);

        if (!$product) {
            throw new NotFoundException('Product');
        }

        $request->validate([
            'quantity' => ['required', 'numeric'],
        ]);

        $product->update([
            'quantity' => $product->quantity + $request['quantity'],
        ]);

        event(new ProductStockChangedByAdminEvent($product));

        return $product;
    }

    public function fomatDate($date)
    {

        $fomatDate = Carbon::createFromFormat('Y-m-d', $date);

        return $fomatDate;
    }

    public function field($request)
    {
        if ($request->has('field')) {
            if ($request->field === 'updated') {
                $field = 'updated_at';
            } elseif ($request->field === 'published') {
                $field = 'published_date';
            } elseif ($request->field === 'created') {
                $field = 'created_at';
            }
            return $field;
        } else {
            throw new Exception('field requied');
        }
    }

    public function getFromDate($request, $query)
    {
        if ($request->has('from')) {

            $field     = $this->field($request);
            $form_date = $this->fomatDate($request->from);
            $query     = $query->whereDate($field, '>=', $form_date);
        }
        return $query;
    }

    public function getToDate($request, $query)
    {
        if ($request->has('to')) {
            $field   = $this->field($request);
            $to_date = $this->fomatDate($request->to);
            $query   = $query->whereDate($field, '<=', $to_date);
        }
        return $query;
    }
    public function getStatus($request, $query)
    {

        if ($request->has('status')) {
            $pattern = '/^\d$/';

            if (!preg_match($pattern, $request->status)) {
                throw new Exception('The input status is incorrect');
            }

            $query = $query->where('status', $request->status);
        }

        return $query;
    }
    public function getStocks($request, $query)
    {

        if ($request->has('in_stock')) {
            if ($request->in_stock === "true") {
                $query = $this->repository->getStock($query);
            } elseif ($request->in_stock === "false") {
                $query = $this->repository->getOutStock($query);
            }
        }

        return $query;
    }

    public function getSku()
    {
        $query_id   = $this->repository->getMaxId();
        $id_product = $query_id + 1;

        $date   = str_replace('-', '', Carbon::now()->format('d-m-Y'));
        $string = str::random(5);
        $sku    = $id_product . $date . $string;

        return $sku;
    }

    public function filterAuthor($request, $query)
    {

        if ($request->has('author_id')) {

            $user = UserProduct::where('user_id', $request['author_id'])->first();

            if (!$user) {
                throw new NotFoundException('User');
            }

            $query = $query->where('id', $user->product_id);
        }

        return $query;
    }

}
