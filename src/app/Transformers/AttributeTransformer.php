<?php

namespace VCComponent\Laravel\Product\Transformers;

use League\Fractal\TransformerAbstract;
use VCComponent\Laravel\Product\Entities\AttributeValue;
use VCComponent\Laravel\Product\Transformers\AttributeValueTransformer;

class AttributeTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'attributesValue',
        'translates',
    ];

    public function __construct($includes = [])
    {
        $this->setDefaultIncludes($includes);
    }

    public function transform($model)
    {
        return [
            'id'         => $model->id,
            'name'       => $model->name,
            'type'       => $model->type,
            'kind'       => $model->kind,
            'timestamps' => [
                'created_at' => $model->created_at,
                'updated_at' => $model->updated_at,
            ],
        ];
    }

    public function includeAttributesValue($model)
    {
        return $this->collection($model->attributeValue, new AttributeValueTransformer());
    }
}
