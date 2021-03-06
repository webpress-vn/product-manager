<?php

namespace VCComponent\Laravel\Product\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use VCComponent\Laravel\Product\Entities\Attribute;
use VCComponent\Laravel\Product\Repositories\AttributeRepository;

/**
 * Class AccountantRepositoryEloquent.
 */
class AttributeRepositoryEloquent extends BaseRepository implements AttributeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Attribute::class;
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
        $attribute = $this->model->find($id);
        if (!$attribute) {
            throw new \Exception('Không tìm thấy thuộc tính !', 1);
        }
        return $attribute;
    }
}
