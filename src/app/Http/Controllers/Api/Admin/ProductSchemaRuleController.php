<?php

namespace VCComponent\Laravel\Product\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use VCComponent\Laravel\Product\Repositories\ProductSchemaRuleRepository;
use VCComponent\Laravel\Product\Transformers\ProductSchemaRuleTransformer;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;

class ProductSchemaRuleController extends ApiController
{
    protected $repository;

    public function __construct(ProductSchemaRuleRepository $repository)
    {
        $this->repository  = $repository;
        $this->entity      = $repository->getEntity();
        $this->transformer = ProductSchemaRuleTransformer::class;

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

        $per_page    = $request->has('per_page') ? (int) $request->get('per_page') : 15;
        $schemarules = $query->paginate($per_page);

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->paginator($schemarules, $transformer);
    }

    function list(Request $request) {
        $query = $this->entity;

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $schemarules = $query->get();

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->paginator($schemarules, $transformer);
    }
}
