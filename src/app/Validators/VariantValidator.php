<?php

namespace VCComponent\Laravel\Product\Validators;

use VCComponent\Laravel\Vicoders\Core\Validators\AbstractValidator;

class VariantValidator extends AbstractValidator
{
    protected $rules = [
        'ADMIN_CREATE'           => [
            'label'      => ['required'],
            'product_id' => ['required'],
        ],
        'ADMIN_UPDATE'           => [
            'label'      => ['required'],
            'product_id' => ['required'],
        ],
        'RULE_ADMIN_CREATE_WITH' => [
            'label'       => ['required'],
            'product_id'  => ['required'],
            'package_ids' => ['required'],
        ],
        'RULE_ADMIN_UPDATE_WITH' => [
            'label'       => ['required'],
            'product_id'  => ['required'],
            'package_ids' => ['required'],
        ],
        'UPDATE_STATUS_ITEM'     => [
            'status' => ['required'],
        ],
    ];
}
