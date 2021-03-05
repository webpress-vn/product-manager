<?php

namespace VCComponent\Laravel\Product\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use VCComponent\Laravel\Product\Repositories\ProductSchemaRepository;
use VCComponent\Laravel\Product\Transformers\ProductSchemaTransformer;
use VCComponent\Laravel\Product\Validators\ProductSchemaValidator;
use VCComponent\Laravel\Product\Entities\ProductMeta;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;

class ProductSchemaController extends ApiController
{
    protected $repository;
    protected $validator;

    public function __construct(ProductSchemaRepository $repository, ProductSchemaValidator $validator)
    {
        $this->repository  = $repository;
        $this->entity      = $repository->getEntity();
        $this->validator   = $validator;
        $this->transformer = ProductSchemaTransformer::class;

        if (config('product.auth_middleware.admin.middleware') !== '') {
            $this->middleware(
                config('product.auth_middleware.admin.middleware'),
                ['except' => config('product.auth_middleware.admin.except')]
            );
        }
    }

    public function index(Request $request )
    {
        $query = $this->entity;

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $per_page   = $request->has('per_page') ? (int) $request->get('per_page') : 20;
        $schemas = $query->paginate($per_page);

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->paginator($schemas, $transformer);
    }

    public function show($id, Request $request)
    {
        $schema = $this->repository->findById($id);

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->item($schema, $transformer);
    }

    public function store(Request $request)
    {
        $this->validator->isValid($request, 'RULE_ADMIN_CREATE');

        $data = $request->all();
        $schema = $this->repository->create($data);

        return $this->response->item($schema, new $this->transformer);
    }

    public function update(Request $request, $id)
    {
        $this->validator->isValid($request, 'RULE_ADMIN_UPDATE');

        $schema_updating = $this->repository->findById($id);

        $data = $request->all();
        $schema = $this->repository->update($data, $id);

        ProductMeta::where('key', $schema_updating->name)->update(['key' => $request->name]);

        return $this->response->item($schema, new $this->transformer);
    }

    public function destroy($id)
    {
        $schema = $this->repository->findById($id);
        ProductMeta::where('key', $schema->name)->delete();
        $schema->delete();

        return $this->success();
    }
}
