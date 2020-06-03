<?php

namespace VCComponent\Laravel\Product\Products;

use VCComponent\Laravel\Product\Entities\Product as EntitiesProduct;
use VCComponent\Laravel\Product\Products\Contracts\Product as ContractsProduct;

class Product implements ContractsProduct
{

    const HOT = 1;
    public function _contract()
    {
    }

    public function getModel()
    {
        if (isset(config('product.models')['product'])) {
            $model = config('product.models.product');
            $query = new $model;
            $query = $query::query();
        } else {
            $model = new EntitiesProduct;
            $query = $model::query();
        }

        return $query;
    }

    public function getHotProducts($limit)
    {
        $query = $this->getModel();
        $query = $query->where('is_hot', self::HOT)->limit($limit)->get();
        return $query;
    }

    public function where($column, $value)
    {
        $query = $this->getModel();
        $query = $query->where($column, $value)->get();

        return $query;
    }

    public function findOrFail($id)
    {
        $query = $this->getModel();
        return $query->findOrFail($id);
    }

    public function toSql()
    {
        $query = $this->getModel();
        return $query->toSql();
    }

    public function get()
    {
        $query = $this->getModel();
        return $query->get();
    }

    public function paginate($perPage)
    {
        $query = $this->getModel();
        return $query->paginate($perPage);
    }

    public function limit($value)
    {
        $query = $this->getModel();

        return $query->limit($value);
    }

    public function orderBy($column, $direction = 'asc')
    {
        $query = $this->getModel();
        return $query->orderBy($column, $direction);
    }

    public function with($relations)
    {
        $query = $this->getModel();
        $query->with($relations);

        return $this;
    }

    public function first()
    {
        $query = $this->getModel();
        return $query->first();
    }

    public function create(array $attributes = [])
    {
        $query = $this->getModel();
        return $query->create($attributes);
    }

    public function firstOrCreate(array $attributes, array $values = [])
    {
        $query = $this->getModel();
        return $query->firstOrCreate($attributes, $values);
    }

    public function update(array $values)
    {
        $query = $this->getModel();
        return $query->update($values);
    }

    public function delete()
    {
        $query = $this->getModel();
        return $query->delete();
    }
}
