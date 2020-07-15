<?php

namespace VCComponent\Laravel\Product\Products;

trait ProductQueryTrait
{
    protected $HOT = 1;
    protected $entity;

    public function hotProductsQuery($limit)
    {
        $query = $this->entity->select('name', 'id', 'slug', 'thumbnail', 'description', 'price', 'original_price')->where('is_hot', $this->HOT)->limit($limit)->get();
        return $query;
    }

    public function relatedProductsQuery($productId, $value)
    {
        $categoryIds = $this->entity->select('name', 'id', 'slug', 'thumbnail', 'description', 'price', 'original_price')->find($productId)->categories->map(function ($cate) {
            return $cate->id;
        });
        return $this->entity->where('id', '<>', $productId)
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            })
            ->limit($value)->get();
    }

    public function getSaleProductsQuery($value)
    {
        return $this->entity->select('name', 'id', 'slug', 'thumbnail', 'description', 'price', 'original_price')->whereColumn('price', '<', 'original_price')->limit($value)->get();
    }
}
