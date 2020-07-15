<?php

namespace VCComponent\Laravel\Product\ViewModels\ProductDetail;

use Carbon\Carbon;
use Illuminate\Support\Str;
use VCComponent\Laravel\Product\Entities\Attribute;
use VCComponent\Laravel\Product\Entities\AttributeValue;
use VCComponent\Laravel\ViewModel\ViewModels\BaseViewModel;

class ProductDetailViewModel extends BaseViewModel
{
    public $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function getDisplayDatetimeAttribute()
    {

        return Carbon::parse($this->product->created_at)->format('d-m-Y h:i:s A');
    }

    public function getLimitDescription($limit = 30)
    {

        return Str::limit($this->product->description, $limit);
    }

    public function getLimitedName($limit = 10)
    {
        return Str::limit($this->product->name, $limit);
    }

    public function isAvailable()
    {
        if ($this->product->quantity) {
            return true;
        }
        return false;
    }

    public function productAttributes()
    {
        $value_ids          = collect($this->product->attributesValue)->pluck('value_id');
        $attributes_value   = AttributeValue::select('id', 'attribute_id', 'label', 'value')->whereIn('id', $value_ids)->get();
        $attributes         = Attribute::select('name', 'id')->whereIn('id', $attributes_value->pluck('attribute_id')->unique())->get();
        $attributes_product = $attributes_value->map(function ($value, $key) use ($attributes) {
            $found = $attributes->search(function ($i) use ($value) {
                return $i->id === $value->attribute_id;
            });

            if ($found !== false) {
                return [$attributes->get($found)->name => $value];
            } else {
                return $value;
            }
        });

        return $attributes_product->map(function ($value, $key) {
            $item_key   = collect($value)->keys()->first();
            $item_value = collect($value)->map(function ($v, $k) {
                return [
                    'id'    => $v['id'],
                    'label' => $v['label'],
                    'value' => $v['value'],
                ];
            })->first();
            return array_merge($item_value, ['attribute' => $item_key]);
        })->groupBy('attribute');
    }

    public function attributeType($attribute_current)
    {
        $attribute = Attribute::select('name', 'type')->where('name', $attribute_current->first()['attribute'])->first();

        return $attribute->type;
    }
}
