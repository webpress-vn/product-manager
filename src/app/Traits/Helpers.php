<?php

namespace VCComponent\Laravel\Product\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use VCComponent\Laravel\Product\Entities\AttributeValue;
use VCComponent\Laravel\Product\Entities\ProductAttribute;

trait Helpers
{

    private function filterProductRequestData(Request $request, $entity)
    {
        $request_data = collect($request->all());
        if (!$request->has('status')) {
            $request_data['status'] = 1;
        }
        $schema = collect($entity->schema());

        $request_data_keys = $request_data->keys();
        $schema_keys       = $schema->keys()->toArray();

        $default_keys = $request_data_keys->diff($schema_keys)->all();

        $data            = [];
        $data['default'] = $request_data->filter(function ($value, $key) use ($default_keys) {
            return in_array($key, $default_keys);
        })->toArray();

        if (array_key_exists('original_price', $data['default']) == false || $data['default']['original_price'] == null) {
            $data['default']['original_price'] = $data['default']['price'];
        }

        if ($request['name']) {
            $data['default']['slug'] = Str::slug($request['name']);
        }

        $data['schema'] = $request_data->filter(function ($value, $key) use ($schema_keys) {
            return in_array($key, $schema_keys);
        })->toArray();

        return $data;
    }

    protected function addAttributes($request, $product)
    {
        if ($request->has('attribute_values')) {
            $this->checkAttributes($request);
            foreach ($request->get('attribute_values') as $value) {

                $attribute = ProductAttribute::where('product_id', $product->id)->where('value_id', $value['value_id'])->where('type', $value['type'])->first();
                $price     = array_key_exists('price', $value) ? $value['price'] : 0;
                $type      = array_key_exists('type', $value) ? $value['type'] : 1;
                if ($attribute) {
                    $attribute->update([
                        'type'  => $value['type'],
                        'price' => $value['price'],
                    ]);
                } else {
                    $product_attribute             = new ProductAttribute;
                    $product_attribute->product_id = $product->id;
                    $product_attribute->value_id   = $value['value_id'];
                    $product_attribute->type       = $type;
                    $product_attribute->price      = $price;
                    $product_attribute->save();
                }
            }
        }
    }

    protected function updateAttributes($request, $product)
    {
        if ($request->has('attribute_values')) {
            $this->checkAttributes($request);

            $old_attributes = ProductAttribute::where('product_id', $product->id)->delete();

            foreach ($request->get('attribute_values') as $value) {
                $price                          = array_key_exists('price', $value) ? $value['price'] : 0;
                $type                           = array_key_exists('type', $value) ? $value['type'] : 1;
                $attributes_product             = new ProductAttribute;
                $attributes_product->product_id = $product->id;
                $attributes_product->value_id   = $value['value_id'];
                $attributes_product->type       = $type;
                $attributes_product->price      = $price;
                $attributes_product->save();
            }
        }
    }

    protected function deleteAttributes($id)
    {
        ProductAttribute::where('product_id', $id)->delete();
    }

    protected function checkAttributes($request)
    {
        $attribute_values = $request->get('attribute_values');
        foreach ($attribute_values as $value) {
            $this->attribute_validator->isValid($value, "RULE_ADMIN_UPDATE");
        }

        $attribute_value_ids     = collect($attribute_values)->pluck('value_id');
        $attribute_values_exists = AttributeValue::whereIn('id', $attribute_value_ids)->get();

        $value_exists = array_values(array_diff($attribute_value_ids->toArray(), $attribute_values_exists->pluck('id')->toArray()));

        if ($value_exists !== []) {
            throw new \Exception("Thuộc tính có id = {$value_exists[0]} không tồn tại", 1);
        }
    }
}
