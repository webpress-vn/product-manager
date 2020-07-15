<?php

namespace VCComponent\Laravel\Product\Validators;

use VCComponent\Laravel\Vicoders\Core\Validators\AbstractValidator;

class AttributeValidator extends AbstractValidator
{
    protected $rules = [
        'RULE_ADMIN_CREATE' => [
            'name' => ['required', 'unique:attributes'],
            'type' => ['required'],
            'kind' => ['required'],
        ],
        'RULE_ADMIN_UPDATE' => [
            'name' => ['required', 'unique:attributes'],
            'type' => ['required'],
            'kind' => ['required'],
        ],
    ];
}
