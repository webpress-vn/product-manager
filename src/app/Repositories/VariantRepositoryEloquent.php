<?php

namespace VCComponent\Laravel\Product\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use VCComponent\Laravel\Product\Repositories\VariantRepository;
use VCComponent\Laravel\Product\Entities\Variant;

/**
 * Class AccountantRepositoryEloquent.
 */
class VariantRepositoryEloquent extends BaseRepository implements VariantRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Variant::class;
    }

    public function getEntity()
    {
        return $this->model;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function findById($id)
    {
        $variant = $this->model->find($id);
        if (!$variant) {
            throw new \Exception('Không tìm thấy thuộc tính !', 1);
        }
        return $variant;
    }

    public function updateStatus($request, $id)
    {
        $status         = $this->find($id);
        $status->status = $request->input('status');
        $status->save();
    }
}
