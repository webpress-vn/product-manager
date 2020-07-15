<?php

namespace VCComponent\Laravel\Product\Transformers;

use League\Fractal\TransformerAbstract;

class AttributeValueTransformer extends TransformerAbstract
{
    protected $availableIncludes = [];

    public function __construct($includes = [])
    {
        $this->setDefaultIncludes($includes);
    }

    public function transform($model)
    {
        return [
            'id'           => (int) $model->id,
            'attribute_id' => $model->attribute_id,
            'label'        => $model->label,
            'value'        => $model->value,
            'timestamps'   => [
                'created_at' => $model->created_at,
                'updated_at' => $model->updated_at,
            ],
        ];
    }
}
