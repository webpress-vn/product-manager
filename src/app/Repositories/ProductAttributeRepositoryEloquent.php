<?php

namespace VCComponent\Laravel\Product\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use VCComponent\Laravel\Product\Entities\ProductAttribute;
use VCComponent\Laravel\Product\Repositories\ProductAttributeRepository;

/**
 * Class AccountantRepositoryEloquent.
 */
class ProductAttributeRepositoryEloquent extends BaseRepository implements ProductAttributeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductAttribute::class;
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
            throw new \Exception('Sản phẩm này không có thuộc tính !', 1);
        }
        return $attribute;
    }
}
