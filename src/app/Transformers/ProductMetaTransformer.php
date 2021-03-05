<?php

namespace VCComponent\Laravel\Product\Transformers;

use League\Fractal\TransformerAbstract;

class ProductMetaTransformer extends TransformerAbstract
{

    public function transform($model)
    {
        return [
            'id'    => (int) $model->id,
            'key'   => $model->key,
            'value' => $model->value,
            'timestamps' => [
                'created_at' => $model->created_at,
                'updated_at' => $model->updated_at,
            ],
        ];
    }
}
