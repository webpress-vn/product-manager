<?php

namespace VCComponent\Laravel\Product\Validators;

use VCComponent\Laravel\Vicoders\Core\Validators\AbstractValidator;

class SchemaValidator extends AbstractValidator
{
    protected $rules = [
        'RULE_ADMIN_CREATE' => [
            'name' => ['required'],
            'type' => ['required'],
            'rule' => ['required'],
            'product' => ['required'],
        ],
        'RULE_ADMIN_UPDATE' => [
            'name' => ['required'],
            'type' => ['required'],
            'rule' => ['required'],
            'product' => ['required'],
        ]
    ];
}
