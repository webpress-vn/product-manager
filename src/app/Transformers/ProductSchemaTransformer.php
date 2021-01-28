<?php

namespace VCComponent\Laravel\Product\Transformers;

use League\Fractal\TransformerAbstract;
use VCComponent\Laravel\Product\Entities\ProductSchemaType;
use VCComponent\Laravel\Product\Entities\ProductSchemaRule;

class ProductSchemaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    public function __construct($includes = [])
    {
        $this->setDefaultIncludes($includes);
    }

    public function transform($model)
    {
        $nameType = ProductSchemaType::where('id', $model->schema_type_id)->get();
        $nameRule = ProductSchemaRule::where('id', $model->schema_rule_id)->get();
        return [
            'id'    => $model->id,
            'name'  => $model->name,
            'label' => $model->label,
            'type'  => $nameType,
            'rule'  => $nameRule,
            'product_type' => $model->product_type,
            'timestamps' => [
                'created_at' => $model->created_at,
                'updated_at' => $model->updated_at,
            ],
        ];
    }
}
