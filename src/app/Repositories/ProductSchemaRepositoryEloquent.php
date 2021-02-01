<?php

namespace VCComponent\Laravel\Product\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use VCComponent\Laravel\Product\Entities\ProductSchema;

class ProductSchemaRepositoryEloquent extends BaseRepository implements ProductSchemaRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductSchema::class;
    }

    public function getEntity()
    {
        return $this->model;
    }

    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function findById($id)
    {
        $schema = $this->model->find($id);
        if (!$schema) {
            throw new \Exception('Không tìm thấy thuộc tính !', 1);
        }
        return $schema;
    }
}
