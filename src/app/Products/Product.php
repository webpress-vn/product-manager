<?php

namespace VCComponent\Laravel\Product\Products;

use Illuminate\Support\Facades\Cache;
use VCComponent\Laravel\Product\Entities\Product as EntitiesProduct;
use VCComponent\Laravel\Product\Products\ProductQueryTrait;

class Product
{
    use ProductQueryTrait;

    protected $cache        = false;
    protected $cacheMinutes = 60;

    public function __construct()
    {
        if (isset(config('product.models')['product'])) {
            $model        = config('product.models.product');
            $this->entity = new $model;
        } else {
            $this->entity = new EntitiesProduct;
        }

        if (config('product.cache')['enabled'] === true) {
            $this->cache     = true;
            $this->timeCache = config('product.cache')['minutes'] ? config('product.cache')['minutes'] * 60 : $this->cacheMinutes * 60;
        }
    }

    public function hotProducts($limit)
    {
        if ($this->cache === true) {
            if (Cache::has('hotProducts') && Cache::get('hotProducts')->count() !== 0) {
                return Cache::get('hotProducts');
            }
            return Cache::remember('hotProducts', $this->timeCache, function () use ($limit) {
                return $this->hotProductsQuery($limit);
            });
        }
        return $this->hotProductsQuery($limit);
    }

    public function relatedProducts($productId, $value)
    {
        if ($this->cache === true) {
            if (Cache::has('relatedProducts') && Cache::get('relatedProducts')->count() !== 0) {
                return Cache::get('relatedProducts');
            }
            return Cache::remember('relatedProducts', $this->timeCache, function () use ($productId, $value) {
                return $this->relatedProductsQuery($productId, $value);
            });
        }
        return $this->relatedProductsQuery($productId, $value);
    }

    public function getSaleProducts($value)
    {
        if ($this->cache === true) {
            if (Cache::has('getSaleProducts') && Cache::get('getSaleProducts')->count() !== 0) {
                return Cache::get('getSaleProducts');
            }
            return Cache::remember('getSaleProducts', $this->timeCache, function () use ($value) {
                return $this->getSaleProductsQuery($value);
            });
        }
        return $this->getSaleProductsQuery($value);
    }
}
