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
    public function includeTranslates($model)
    {

        $data       = $model->translates->groupBy('language_id');
        $attributes = [];
        foreach ($data as $d) {
            $keyed = $d->mapWithKeys(function ($item) {
                return [$item['field'] => $item['value']];
            });

            $keyed->put('id', $model->id);
            array_push($attributes, $keyed);
        }

        return $this->collection($attributes[0], new AttributeTransformer());
    }
}
