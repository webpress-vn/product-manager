<?php

namespace VCComponent\Laravel\Product\Validators;

use VCComponent\Laravel\Vicoders\Core\Validators\AbstractValidator;

class ProductAttributeValidator extends AbstractValidator
{
    protected $rules = [
        'RULE_ADMIN_CREATE' => [
            'value_id' => ['required'],
        ],
        'RULE_ADMIN_UPDATE' => [
            'value_id' => ['required'],
        ],
    ];
}
