<?php

namespace VCComponent\Laravel\Product\Transformers;

use League\Fractal\TransformerAbstract;
use VCComponent\Laravel\Product\Entities\Product;
use VCComponent\Laravel\Product\Entities\VariantProduct;
use VCComponent\Laravel\Product\Transformers\ProductTransformer;

class VariantTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'products'
    ];

    public function __construct($includes = [])
    {
        $this->setDefaultIncludes($includes);
    }

    public function transform($model)
    {
        return [
            'id'         => (int) $model->id,
            'label'      => $model->label,
            'thumbnail'  => $model->thumbnail,
            'price'      => (int) $model->price,
            'type'       => $model->type,
            'product_id' => (int) $model->product_id,
            'status'     => (int) $model->status,
            'timestamps' => [
                'created_at' => $model->created_at,
                'updated_at' => $model->updated_at,
            ],
        ];
    }

    public function includeProducts($model)
    {
        $product_ids = VariantProduct::where(['variant_id' => $model->id, 'variantable_type' => 'products'])->pluck('variantable_id');

        $product_related =  Product::whereIn('id', $product_ids)->get();
        return $this->collection($product_related, new ProductTransformer());
    }
}
