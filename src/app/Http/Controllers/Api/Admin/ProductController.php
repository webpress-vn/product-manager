<?php

namespace VCComponent\Laravel\Product\Http\Controllers\Api\Admin;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use VCComponent\Laravel\Export\Services\Export\Export;
use VCComponent\Laravel\Product\Entities\ProductSchema;
use VCComponent\Laravel\Product\Entities\ProductSchemaRule;
use VCComponent\Laravel\Product\Entities\ProductSchemaType;
use VCComponent\Laravel\Product\Events\ProductCreatedByAdminEvent;
use VCComponent\Laravel\Product\Events\ProductDeletedEvent;
use VCComponent\Laravel\Product\Events\ProductStockChangedByAdminEvent;
use VCComponent\Laravel\Product\Events\ProductUpdatedByAdminEvent;
use VCComponent\Laravel\Product\Repositories\ProductRepository;
use VCComponent\Laravel\Product\Traits\Helpers;
use VCComponent\Laravel\Product\Transformers\ProductSchemaTransformer;
use VCComponent\Laravel\Product\Transformers\ProductTransformer;
use VCComponent\Laravel\Product\Validators\ProductAttributeValidator;
use VCComponent\Laravel\Product\Validators\ProductValidator;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;
use VCComponent\Laravel\Vicoders\Core\Exceptions\NotFoundException;
use VCComponent\Laravel\Vicoders\Core\Exceptions\PermissionDeniedException;

class ProductController extends ApiController
{
    use Helpers;

    public function __construct(ProductRepository $repository, ProductValidator $validator, ProductAttributeValidator $attribute_validator, Request $request)
    {
        $this->repository          = $repository;
        $this->entity              = $repository->getEntity();
        $this->validator           = $validator;
        $this->attribute_validator = $attribute_validator;
        $this->productType         = $this->getProductTypesFromRequest($request);

        if (!empty(config('product.auth_middleware.admin'))) {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToUse($user)) {
                throw new PermissionDeniedException();
            }

            foreach (config('product.auth_middleware.admin') as $middleware) {
                $this->middleware($middleware['middleware'], ['except' => $middleware['except']]);
            }
        }
        if (isset(config('product.transformers')['product'])) {
            $this->transformer = config('product.transformers.product');
        } else {
            $this->transformer = ProductTransformer::class;
        }
    }

    public function export(Request $request)
    {

        if (config('product.auth_middleware.admin') !== []) {
            $user = $this->getAuthenticatedUser();
            // if (!$this->entity->ableToShow($user)) {
            //     throw new PermissionDeniedException();
            // }
        }
        $this->validator->isValid($request, 'RULE_EXPORT');

        $data     = $request->all();
        $products = $this->getReportProducts($request);

        $args = [
            'data'      => $products,
            'label'     => $request->label ? $data['label'] : 'products',
            'extension' => $request->extension ? $data['extension'] : 'Xlsx',
        ];
        $export = new export($args);
        $url    = $export->export();

        if (config('product.test_mode')) {
            return $this->response->array(['data' => $products]);
        } else {
            return $this->response->array(['url' => $url]);
        }
    }

    private function getReportProducts(Request $request)
    {

        $fields = [
            'products.name as `Tên sản phẩm`',
            'products.quantity as `Số lượng`',
            'products.sold_quantity as `Số lượng đã bán`',
            'products.product_type as `Loại sản phẩm`',
            'products.code as `Mã sản phẩm`',
            'products.thumbnail as `Link ảnh`',
            'products.price as `Gía bán`',
            'products.unit_price as `Đơn vị tính`',
            'users.username as `Người tạo`',
        ];
        $fields = implode(', ', $fields);

        $query = $this->entity;
        $query = $query->select(DB::raw($fields));
        $query = $this->applyQueryScope($query, 'product_type', $this->productType);
        $query = $this->getFromDate($request, $query);
        $query = $this->getToDate($request, $query);
        $query = $this->getStocks($request, $query);
        $query = $this->getStatus($request, $query);
        $query = $this->whereHasCategory($request, $query);

        $query = $this->filterAuthor($request, $query);

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name', 'description', 'price'], $request, ['productMetas' => ['value']]);

        $query = $query->leftJoin('users', function ($join) {
            $join->on('products.author_id', '=', 'users.id');
        });

        $products = $query->get()->toArray();

        return $products;
    }

    public function index(Request $request)
    {
        $query = $this->entity->with('productMetas');
        $query = $this->applyQueryScope($query, 'product_type', $this->productType);
        $query = $this->getFromDate($request, $query);
        $query = $this->getToDate($request, $query);
        $query = $this->getStatus($request, $query);
        $query = $this->getStocks($request, $query);
        $query = $this->filterAuthor($request, $query);
        $query = $this->whereHasCategory($request, $query);

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name', 'description', 'price'], $request, ['productMetas' => ['value']]);
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

    public function whereHasCategory($request, $query)
    {
        if ($request->category) {
            $query = $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        return $query;
    }

    function list(Request $request) {
        $query = $this->entity;
        $query = $this->applyQueryScope($query, 'product_type', $this->productType);
        $query = $this->getFromDate($request, $query);
        $query = $this->getToDate($request, $query);
        $query = $this->getStocks($request, $query);
        $query = $this->getStatus($request, $query);
        $query = $this->whereHasCategory($request, $query);

        $query = $this->filterAuthor($request, $query);

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name', 'description', 'price'], $request, ['productMetas' => ['value']]);
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
        if (!empty(config('product.auth_middleware.admin'))) {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToShow($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        $query   = $this->entity;
        $product = $query->where('id', $id)->first();

        if (!$product) {
            throw new NotFoundException($this->productType);
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
        $user = null;

        if (!empty(config('product.auth_middleware.admin'))) {
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

        $data['default']['author_id'] = $user ? $user->id : $request->get(
            'author_id'
        );
        $data['default']['product_type'] = $this->productType;

        $product = $this->repository->create($data['default']);

        if (count($no_rule_fields)) {
            foreach ($no_rule_fields as $key => $value) {
                $product->productMetas()->updateOrCreate([
                    'key'   => $key,
                    'value' => null,
                ], ['value' => '']);
            }
        }
        if (count($data['schema'])) {
            foreach ($data['schema'] as $key => $data) {
                $value = (!$data) ? 'image' : $data;
                $product->productMetas()->updateOrCreate([
                    'key' => $key,
                ], [
                    'value' => $value,
                ]);
            }
        }

        $this->addAttributes($request, $product);
        $this->addVariant($request, $product);

        event(new ProductCreatedByAdminEvent($product));

        return $this->response->item($product, new $this->transformer);
    }

    public function update(Request $request, $id)
    {
        if (!empty(config('product.auth_middleware.admin'))) {

            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToUpdateItem($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        $product = $this->entity->find($id);
        if (!$product) {
            throw new NotFoundException('Product');
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

        $this->updateAttributes($request, $product);
        $this->updateVariant($request, $product);

        event(new ProductUpdatedByAdminEvent($product));

        return $this->response->item($product, new $this->transformer);
    }

    public function destroy(Request $request, $id)
    {

        if (!empty(config('product.auth_middleware.admin'))) {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToDelete($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        $product = $this->repository->findWhere(['id' => $id])->first();
        if (!$product) {
            throw new NotFoundException('Product');
        }

        $this->repository->delete($id);
        $this->deleteAttributes($id);
        $this->deleteVariant($id);

        event(new ProductDeletedEvent($product));

        return $this->success();
    }

    public function bulkUpdateStatus(Request $request)
    {
        if (!empty(config('product.auth_middleware.admin'))) {
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
        if (!empty(config('product.auth_middleware.admin'))) {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToUpdateItem($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        $product = $this->repository->findWhere(['id' => $id])->first();

        if (!$product) {
            throw new NotFoundException('Product');
        }

        $this->validator->isValid($request, 'UPDATE_STATUS_ITEM');

        $data            = $request->all();
        $product->status = $data['status'];
        $product->save();

        return $this->success();
    }

    public function changeDatetime(Request $request, $id)
    {
        if (!empty(config('product.auth_middleware.admin'))) {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToUpdateItem($user, $id)) {
                throw new PermissionDeniedException();
            }
        }

        $product = $this->entity->where(['id' => $id])->first();

        if (!$product) {
            throw new NotFoundException('Product');
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
        $product = $this->repository->findWhere(['id' => $id])->first();

        if (!$product) {
            throw new NotFoundException('Product');
        }

        if ($product->quantity <= 0) {
            return response()->json(['in_stock' => false]);
        }

        return response()->json(['in_stock' => true]);
    }

    public function changeQuantity(Request $request, $id)
    {
        $product = $this->repository->findWhere(['id' => $id])->first();

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
        $product = $this->repository->findWhere(['id' => $id])->first();

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
                $field = 'products.updated_at';
            } elseif ($request->field === 'published') {
                $field = 'products.published_date';
            } elseif ($request->field === 'created') {
                $field = 'products.created_at';
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

            $query = $query->where(['products.status' => $request->status, 'product_type' => $this->productType]);
        }

        return $query;
    }

    public function getStocks($request, $query)
    {

        if ($request->has('in_stock')) {
            if ($request->in_stock === "true") {
                $query = $this->repository->where('product_type', $this->productType)->getStock($query);
            } elseif ($request->in_stock === "false") {
                $query = $this->repository->where('product_type', $this->productType)->getOutStock($query);
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
            $query = $query->where('id', $user->product_id);
        }

        return $query;
    }

    public function bulkDelete(Request $request)
    {
        $this->validator->isValid($request, 'RULE_IDS');

        $ids      = $request->ids;
        $products = $this->entity::whereIn('id', $ids);
        if (count($ids) > $products->get()->count()) {
            throw new NotFoundException('Product');
        }
        $products->delete();
        return $this->success();
    }

    public function restore($id)
    {
        $product = $this->entity::where('id', $id)->get();
        if (count($product) > 0) {
            throw new NotFoundException('Product');
        }

        $this->repository->restore($id);
        return $this->success();
    }

    public function bulkRestore(Request $request)
    {
        $this->validator->isValid($request, 'RULE_IDS');
        $ids      = $request->ids;
        $products = $this->entity->onlyTrashed()->whereIn("id", $ids)->get();

        if (count($ids) > $products->count()) {
            throw new NotFoundException('Product');
        }

        $product = $this->repository->bulkRestore($ids);
        return $this->success();
    }

    public function getAllTrash()
    {
        $trash = $this->entity->onlyTrashed();

        $products = $trash->get();

        return $this->response->collection($products, new $this->transformer());
    }

    public function trash(Request $request)
    {
        $trash = $this->entity->onlyTrashed();

        if ($trash->first() === null) {
            $product = [];
        }
        $trash    = $this->applySearchFromRequest($trash, ['name', 'description', 'price'], $request);
        $per_page = $request->has('per_page') ? (int) $request->get('per_page') : 15;
        $product  = $trash->paginate($per_page);

        return $this->response->paginator($product, new $this->transformer());
    }

    public function forceBulkDelete(Request $request)
    {
        $this->validator->isValid($request, 'RULE_IDS');
        $ids      = $request->ids;
        $products = $this->entity->whereIn("id", $ids);
        if (count($ids) > $products->get()->count()) {
            throw new NotFoundException('Product');
        }
        $product = $products->forceDelete();
        return $this->success();
    }

    public function deleteAllTrash()
    {
        $products = $this->entity->onlyTrashed()->forceDelete();
        return $this->success();
    }

    public function deleteTrash($id)
    {
        $product = $this->entity->onlyTrashed()->where("id", $id)->first();
        if (!$product) {
            throw new NotFoundException('Product');
        }
        $product = $this->repository->deleteTrash($id);
        return $this->success();
    }

    public function bulkDeleteTrash(Request $request)
    {
        $this->validator->isValid($request, 'RULE_IDS');
        $ids      = $request->ids;
        $products = $this->entity->onlyTrashed()->whereIn("id", $ids)->get();
        if (count($ids) > $products->count()) {
            throw new NotFoundException('Product');
        }
        $product = $this->repository->bulkDeleteTrash($ids);
        return $this->success();
    }

    public function forceDelete($id)
    {

        $product = $this->entity->where('id', $id)->first();
        if (!$product) {
            throw new NotFoundException('Product');
        }

        $this->repository->forceDelete($id);

        return $this->success();
    }

    public function getType()
    {
        $productTypes = $this->entity->productTypes();
        return response()->json(['data' => $productTypes]);
    }

    public function getFieldMeta()
    {
        $data = ProductSchema::get();
        return $this->response->collection($data, new ProductSchemaTransformer());
    }
}
