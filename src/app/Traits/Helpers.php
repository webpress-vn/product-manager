<?php

namespace VCComponent\Laravel\Product\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use VCComponent\Laravel\Product\Entities\AttributeValue;
use VCComponent\Laravel\Product\Entities\ProductSchema;
use VCComponent\Laravel\Product\Entities\ProductAttribute;
use VCComponent\Laravel\Product\Entities\ProductSchemaRule;
use VCComponent\Laravel\Product\Entities\ProductSchemaType;
use VCComponent\Laravel\Product\Entities\Variant;
use VCComponent\Laravel\Product\Entities\VariantProduct;
use VCComponent\Laravel\Product\Validators\VariantValidator;

trait Helpers
{

    private function filterProductRequestData(Request $request, $entity)
    {
        $request_data = collect($request->all());
        if (!$request->has('status')) {
            $request_data['status'] = 1;
        }

        $type = $this->getProductTypesFromRequest($request);
        $key  = ucwords($type) . 'Schema';

        $fieldMeta = ProductSchema::all();
        $meta = array();
        foreach ($fieldMeta as $item) {
            $metaType = substr(trim(trim(trim(ProductSchemaType::where('id', $item->schema_type_id)->select('name')->get(),"[]"), "{}"), '""'), 7);
            $metaRule = substr(trim(trim(trim(ProductSchemaRule::where('id', $item->schema_rule_id)->select('name')->get(),"[]"), "{}"), '""'), 7);
            array_push($meta, [ 'data' => [$item->name => [
                    'type'  => $metaType,
                    'label' => $item->label,
                    'rule'  => $metaRule,
                    'name'  => $item->name,
                    'productType' => $item->product_type
                ]
                ]
                ]
            );
        }

        if (method_exists($entity, $key)) {
            $schema = collect($entity->$key());
        } else {
            $schema = collect(json(trim(json_encode($meta), '[]')));
        }

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

    private function applyQueryScope($query, $field, $value)
    {
        $query = $query->where($field, $value);

        return $query;
    }

    private function getProductTypesFromRequest(Request $request)
    {
        $path_items  = collect(explode('/', $request->path()));

        $check_admin = $path_items->filter(function ($item) {
            return $item === 'admin';
        })->count();

        if ($check_admin) {
            if (config('product.namespace') === '' || config('product.namespace') === null) {
                $path_items = $this->handlingPathArray($path_items, 3);
            } else {
                $path_items = $this->handlingPathArray($path_items, 4);
            }
        } else {
            if (config('product.namespace') === '' || config('product.namespace') === null) {
                $path_items = $this->handlingPathArray($path_items, 2);
            } else {
                $path_items = $this->handlingPathArray($path_items, 3);
            }
        }

        $type = $path_items->last();

        return $type;
    }

    private function handlingPathArray($path_array, $base)
    {
        switch ($path_array->count()) {
            case $base + 1:
                $path_array->pop();
                break;
            case $base + 2:
                $path_array->pop();
                $path_array->pop();
                break;
        }

        return $path_array;
    }

    private function getTypeProduct($request)
    {
        if (config('product.models.product') !== null) {
            $model_class = config('product.models.product');
        } else {
            $model_class = Product::class;
        }

        $model        = new $model_class;
        $productTypes = $model->productTypes();
        $path_items   = collect(explode('/', $request->path()));
        $type         = 'products';

        foreach ($productTypes as $value) {
            foreach ($path_items as $item) {
                if ($value === $item) {
                    $type = $value;
                }
            }
        }

        return $type;
    }

    protected function addVariant(Request $request, $product)
    {
        if ($request->has('variants')) {

            foreach ($request->get('variants') as $value) {

                $validator = new VariantValidator;
                $validator->isValid($value, 'RULE_ADMIN_CREATE_WITH');

                foreach ($value['package_ids'] as $product_id) {
                    $product_exists = Product::find($product_id);
                    if(!$product_exists){
                        throw new \Exception ("Lỗi thêm combo : Không tìm thấy sản phẩm !",1 );
                    }
                }

                $variant             = new Variant;
                $variant->product_id = $product->id;
                $variant->label      = $value['label'];
                $variant->thumbnail  = array_key_exists('thumbnail', $value) ? $value['thumbnail'] : null;
                $variant->type       = array_key_exists('type', $value) ? $value['type'] : null;
                $variant->price      = array_key_exists('price', $value) ? $value['price'] : null;
                $variant->save();

                foreach($value['package_ids'] as $product_vrt) {
                    $data = [
                        'variant_id'       => $variant->id,
                        'variantable_id'   => $product_vrt,
                        'variantable_type' => 'products',
                    ];

                    VariantProduct::create($data);
                }
            }


        }
    }

    protected function updateVariant(Request $request, $product)
    {
        if ($request->has('variants')) {

            foreach ($request->get('variants') as $value) {
               $validator = new VariantValidator;
               $validator->isValid($value, 'RULE_ADMIN_UPDATE_WITH');
            }

            $old_variant = Variant::where('product_id', $product->id)->delete();

            foreach ($request->get('variants') as $value) {
                foreach ($value['package_ids'] as $product_id) {
                    $product_exists = Product::find($product_id);
                    if(!$product_exists){
                        throw new \Exception ("Lỗi thêm combo : Không tìm thấy sản phẩm !",1 );
                    }
                }

                $variant             = new Variant;
                $variant->product_id = $product->id;
                $variant->label      = $value['label'];
                $variant->thumbnail  = array_key_exists('thumbnail', $value) ? $value['thumbnail'] : null;
                $variant->type       = array_key_exists('type', $value) ? $value['type'] : null;
                $variant->price      = array_key_exists('price', $value) ? $value['price'] : null;
                $variant->save();

                foreach($value['package_ids'] as $product_vrt) {
                    $data = [
                        'variant_id'       => $variant->id,
                        'variantable_id'   => $product_vrt,
                        'variantable_type' => 'products',
                    ];

                    VariantProduct::create($data);
                }
            }


        }
    }

    protected function deleteVariant($id)
    {
        Variant::where('product_id', $id)->delete();
    }
}
