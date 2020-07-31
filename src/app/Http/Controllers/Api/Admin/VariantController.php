<?php

namespace VCComponent\Laravel\Product\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use VCComponent\Laravel\Product\Repositories\VariantRepository;
use VCComponent\Laravel\Product\Transformers\VariantTransformer;
use VCComponent\Laravel\Product\Validators\VariantValidator;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;

class VariantController extends ApiController
{
    public function __construct(VariantRepository $repository, VariantValidator $validator)
    {
        $this->repository  = $repository;
        $this->entity      = $repository->getEntity();
        $this->validator   = $validator;
        $this->transformer = VariantTransformer::class;

        if (config('product.auth_middleware.admin.middleware') !== '') {
            $this->middleware(
                config('product.auth_middleware.admin.middleware'),
                ['except' => config('product.auth_middleware.admin.except')]
            );
        }

        if (config('product.auth_middleware.admin.middleware') !== '') {
            $user = $this->getAuthenticatedUser();
            if (!$this->entity->ableToShow($user, $id)) {
                throw new PermissionDeniedException();
            }
        }
    }

    public function index(Request $request)
    {
        $query = $this->entity;

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['label', 'type', 'price'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $per_page = $request->has('per_page') ? (int) $request->get('per_page') : 15;
        $variants = $query->paginate($per_page);

        return $this->response->paginator($variants, new $this->transformer);
    }

    function list(Request $request) {
        $query = $this->entity;

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['label', 'type', 'price'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $variants = $query->get();

        return $this->response->item($variants, new $this->transformer);
    }

    public function show(Request $request, $id)
    {
        $query   = $this->entity;
        $variant = $query->where('id', $id)->first();

        if (!$variant) {
            throw new \Exception('Không tìm thấy giá trị !');
        }

        return $this->response->item($variant, new $this->transformer);
    }

    public function store(Request $request)
    {
        $this->validator->isValid($request, 'ADMIN_CREATE');

        $data    = $request->all();
        $variant = $this->repository->create($data);

        return $this->response->item($variant, new $this->transformer);
    }

    public function update(Request $request, $id)
    {
        $this->validator->isValid($request, 'ADMIN_UPDATE');

        $this->repository->findById($id);

        $data    = $request->all();
        $variant = $this->repository->update($data, $id);

        return $this->response->item($variant, new $this->transformer);
    }

    public function destroy($id)
    {
        $variant = $this->repository->findById($id);

        $variant->delete();

        return $this->success();
    }

    public function updateStatus(Request $request, $id)
    {
        $this->validator->isValid($request, 'UPDATE_STATUS_ITEM');

        $this->repository->findById($id);

        $this->repository->updateStatus($request, $id);

        return $this->success();
    }

}
