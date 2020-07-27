<?php

namespace VCComponent\Laravel\Product\Http\Controllers\Api\Admin;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use VCComponent\Laravel\Product\Repositories\AttributeValueRepository;
use VCComponent\Laravel\Product\Transformers\AttributeValueTransformer;
use VCComponent\Laravel\Product\Validators\AttributeValueValidator;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;

class AttributeValueController extends ApiController
{
    protected $repository;
    protected $validator;

    public function __construct(AttributeValueRepository $repository, AttributeValueValidator $validator)
    {
        $this->repository  = $repository;
        $this->entity      = $repository->getEntity();
        $this->validator   = $validator;
        $this->transformer = AttributeValueTransformer::class;

        if (config('product.auth_middleware.admin.middleware') !== '') {
            $this->middleware(
                config('product.auth_middleware.admin.middleware'),
                ['except' => config('product.auth_middleware.admin.except')]
            );
        }
    }

    public function index(Request $request)
    {
        $this->getConfigLanguage($request);
        $query = $this->entity;

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $per_page   = $request->has('per_page') ? (int) $request->get('per_page') : 15;
        $attributes = $query->paginate($per_page);

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->paginator($attributes, $transformer);
    }

    public function show($id, Request $request)
    {
        $attribute = $this->repository->findById($id);

        if ($request->has('includes')) {
            $transformer = new $this->transformer(explode(',', $request->get('includes')));
        } else {
            $transformer = new $this->transformer;
        }

        return $this->response->item($attribute, $transformer);
    }

    public function store(Request $request)
    {

        $this->validator->isValid($request, 'RULE_ADMIN_CREATE');

        $values = $this->repository->where('label', $request->get('label'))->get();

        if ($values) {
            foreach ($values as $value) {
                if ($value->attribute_id == $request->get('attribute_id')) {
                    throw new \Exception("Giá trị của thuộc tính này đã tồn tại !", 1);
                }
            }
        }

        $data = $request->all();

        try {
            $attribute = $this->repository->create($data);
        } catch (QueryException $e) {
            throw new \Exception("Thuộc tính nhập vào không tồn tại !", 1);
        }

        return $this->response->item($attribute, new $this->transformer);
    }

    public function update(Request $request, $id)
    {
        $this->validator->isValid($request, 'RULE_ADMIN_UPDATE');

        $this->repository->findById($id);

        $data = $request->all();

        try {
            $attribute = $this->repository->update($data, $id);
        } catch (QueryException $e) {
            throw new \Exception("Thuộc tính nhập vào không tồn tại !", 1);
        }

        return $this->response->item($attribute, new $this->transformer);
    }

    public function destroy($id)
    {
        $attribute = $this->repository->findById($id);

        $attribute->delete();

        return $this->success();
    }

}
