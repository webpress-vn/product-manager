<?php

namespace VCComponent\Laravel\Product\Transformers;

use League\Fractal\TransformerAbstract;
use VCComponent\Laravel\Product\Entities\ProductSchemaRule;
use VCComponent\Laravel\Product\Entities\ProductSchemaType;
use VCComponent\Laravel\Product\Transformers\ProductSchemaRuleTransformer;
use VCComponent\Laravel\Product\Transformers\ProductSchemaTypeTransformer;

class ProductSchemaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'schemaRule',
        'schemaType',
    ];

    public function __construct($includes = [])
    {
        $this->setDefaultIncludes($includes);
    }

    public function transform($model)
    {
        return [
            'id'             => $model->id,
            'name'           => $model->name,
            'label'          => $model->label,
            'schema_type_id' => $model->schema_type_id,
            'schema_rule_id' => $model->schema_rule_id,
            'product_type'   => $model->product_type,
            'timestamps'     => [
                'created_at' => $model->created_at,
                'updated_at' => $model->updated_at,
            ],
        ];
    }

    public function includeSchemaType($model)
    {
        if ($model->schemaType) {
            return $this->item($model->schemaType, new ProductSchemaTypeTransformer());
        }
    }

    public function includeSchemaRule($model)
    {
        if ($model->schemaRule) {
            return $this->item($model->schemaType, new ProductSchemaRuleTransformer());
        }
    }
}
