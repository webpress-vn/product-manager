<?php

namespace VCComponent\Laravel\Product\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use VCComponent\Laravel\Product\Repositories\ProductSchemaTypeRepository;
use VCComponent\Laravel\Product\Transformers\ProductSchemaTypeTransformer;

use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;

class ProductSchemaTypeController extends ApiController
{
    protected $repository;

    public function __construct(ProductSchemaTypeRepository $repository)
    {
        $this->repository  = $repository;
        $this->entity      = $repository->getEntity();
        $this->transformer = ProductSchemaTypeTransformer::class;

        if (config('product.auth_middleware.admin.middleware') !== '') {
            $this->middleware(
                config('product.auth_middleware.admin.middleware'),
                ['except' => config('product.auth_middleware.admin.except')]
            );
        }
    }

    public function index(Request $request)
    {
        $query = $this->entity;

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $per_page   = $request->has('per_page') ? (int) $request->get('per_page') : 15;
        $schematypes = $query->paginate($per_page);

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->paginator($schematypes, $transformer);
    }

    public function list(Request $request)
    {
        $query = $this->entity;

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $schematypes = $query->get();

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->paginator($schematypes, $transformer);
    }
}
