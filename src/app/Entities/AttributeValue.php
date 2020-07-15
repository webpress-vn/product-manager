<?php

namespace VCComponent\Laravel\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use VCComponent\Laravel\Language\Traits\HasLanguageTrait;
use VCComponent\Laravel\Product\Entities\Attribute;
use VCComponent\Laravel\Product\Entities\ProductAttribute;

class AttributeValue extends Model
{
    // use HasLanguageTrait;
    protected $fillable = [
        'attribute_id',
        'label',
        'value',
    ];

    public function attribute()
    {
        return $this->beLongsTo(Attribute::class);
    }

    public function getPrice($product)
    {
        $price             = 0;
        $product_attribute = ProductAttribute::select('type', 'price')->where('product_id', $product->id)->where('value_id', $this->id)->first();
        $attribute_price   = number_format($product_attribute->price);
        if ($product_attribute->type === ProductAttribute::PRICE_ORIGIN) {
            $price = "+ 0";
        } else if ($product_attribute->type === ProductAttribute::PRICE_MINUS) {
            $price = "- " . $attribute_price;
        } else {
            $price = "+ " . $attribute_price;
        }
        return ($price);
    }
}
