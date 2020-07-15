<?php

namespace VCComponent\Laravel\Product\Validators;

use VCComponent\Laravel\Vicoders\Core\Validators\AbstractValidator;

class AttributeValueValidator extends AbstractValidator
{
    protected $rules = [
        'RULE_ADMIN_CREATE' => [
            'attribute_id' => ['required'],
            'label'        => ['required'],
        ],
        'RULE_ADMIN_UPDATE' => [
            'attribute_id' => ['required'],
            'label'        => ['required'],
        ],
    ];
}
