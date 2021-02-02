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
        ];
    }
}
