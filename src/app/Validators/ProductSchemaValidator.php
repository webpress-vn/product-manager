<?php

namespace VCComponent\Laravel\Product\Validators;

use VCComponent\Laravel\Vicoders\Core\Validators\AbstractValidator;

class ProductSchemaValidator extends AbstractValidator
{
    protected $rules = [
        'RULE_ADMIN_CREATE'  => [
            'label'          => ['required'],
            'schema_type_id' => ['required'],
            'schema_rule_id' => ['required'],
            'product_type'   => ['required'],
        ],
        'RULE_ADMIN_UPDATE'  => [

        ]
    ];
}
