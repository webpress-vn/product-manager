<?php

namespace VCComponent\Laravel\Product\Entities;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use VCComponent\Laravel\Product\Contracts\ProductManagement;
use VCComponent\Laravel\Product\Contracts\ProductSchema;
use VCComponent\Laravel\Product\Entities\ProductAttribute;
use VCComponent\Laravel\Product\Entities\ProductMeta;
use VCComponent\Laravel\Product\Traits\ProductManagementTrait;
use VCComponent\Laravel\Product\Traits\ProductSchemaTrait;
use VCComponent\Laravel\Tag\Traits\HasTagsTraits;

class Product extends Model implements Transformable, ProductSchema, ProductManagement
{
    use TransformableTrait, ProductSchemaTrait, ProductManagementTrait, Sluggable, SluggableScopeHelpers, HasTagsTraits, SoftDeletes;

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
        return [
            'alt_image' => [
                'type' => 'string',
                'rule' => [],
            ],
        ];
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

    public function productHasSale()
    {
        $price        = $this->price;
        $origin_price = $this->original_price;

        $percent_sale = null;
        if ($price < $origin_price) {
            $caculate     = (100 - ($price * 100 / $origin_price));
            $integer_part = explode('.', $caculate);

            if ($integer_part[0] <= 3) {
                $percent_sale = "- " . number_format($origin_price - $price) . " đ";
            } else {
                $percent_sale = "- " . $integer_part[0] . " %";
            }
        }

        return $percent_sale;
    }

    public function productPrice()
    {
        $price = $this->price;
        return number_format($price);
    }

    public function attributesValue()
    {
        return $this->hasMany(ProductAttribute::class);
    }
}
