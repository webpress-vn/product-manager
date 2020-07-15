<?php

namespace VCComponent\Laravel\Product\Transformers;

use League\Fractal\TransformerAbstract;
use VCComponent\Laravel\Product\Transformers\AttributeValueTransformer;

class ProductAttributeTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'attributeItem'
    ];

    public function __construct($includes = [])
    {
        $this->setDefaultIncludes($includes);
    }

    public function transform($model)
    {
        return [
            'id'         => (int) $model->id,
            'product_id' => $model->product_id,
            'value_id'   => $model->value_id,
            'type'       => $model->type,
            'price'      => $model->price,
            'timestamps' => [
                'created_at' => $model->created_at,
                'updated_at' => $model->updated_at,
            ],
        ];
    }

    public function includeAttributeItem($model)
    {
        return $this->item($model->attributeItem, new AttributeValueTransformer());
    }
}
