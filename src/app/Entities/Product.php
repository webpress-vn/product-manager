<?php

namespace VCComponent\Laravel\Product\Entities;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use VCComponent\Laravel\Product\Contracts\ProductManagement;
use VCComponent\Laravel\Product\Contracts\ProductSchema;
use VCComponent\Laravel\Product\Entities\ProductMeta;
use VCComponent\Laravel\Product\Traits\ProductManagementTrait;
use VCComponent\Laravel\Product\Traits\ProductSchemaTrait;
use VCComponent\Laravel\Tag\Traits\HasTagsTraits;

class Product extends Model implements Transformable, ProductSchema, ProductManagement
{
    use TransformableTrait, ProductSchemaTrait, ProductManagementTrait, Sluggable, SluggableScopeHelpers , HasTagsTraits;

    const STATUS_PENDING   = 0;
    const STATUS_PUBLISHED = 1;

    const HOT = 1;

    protected $fillable = [
        'id',
        'name',
        'code',
        'description',
        'status',
        'price',
        'original_price',
        'thumbnail',
        'quantity',
        'sold_quantity',
        'is_hot',
        'author_id',
        'published_date',
        'sku',
    ];

    public function schema()
    {
        return [];
    }

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function scopeHot($query)
    {
        return $query->where('is_hot', self::HOT);
    }

    public function productMeta()
    {
        return $this->hasOne(ProductMeta::class);
    }
}
