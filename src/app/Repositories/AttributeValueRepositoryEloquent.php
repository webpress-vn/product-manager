<?php

namespace VCComponent\Laravel\Product\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use VCComponent\Laravel\Product\Entities\AttributeValue;
use VCComponent\Laravel\Product\Repositories\AttributeValueRepository;

/**
 * Class AccountantRepositoryEloquent.
 */
class AttributeValueRepositoryEloquent extends BaseRepository implements AttributeValueRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AttributeValue::class;
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
        $value = $this->model->find($id);
        if (!$value) {
            throw new \Exception('Không tìm thấy giá trị !', 1);
        }
        return $value;
    }
}
