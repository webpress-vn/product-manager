<?php

namespace VCComponent\Laravel\Product\Validators;

use Exception;
use Illuminate\Support\Facades\Validator;
use VCComponent\Laravel\Vicoders\Core\Validators\AbstractValidator;
use VCComponent\Laravel\Vicoders\Core\Validators\ValidatorInterface;

class ProductValidator extends AbstractValidator
{
    protected $rules = [
        ValidatorInterface::RULE_ADMIN_CREATE  => [
            'name'        => ['required'],
            'description' => [],
            'price'       => ['required'],
            'sku'         => ['unique:products,sku'],
        ],
        ValidatorInterface::RULE_ADMIN_UPDATE  => [
            'name'        => ['required'],
            'description' => [],
        ],
        ValidatorInterface::RULE_CREATE        => [
            'name'        => ['required'],
            'description' => [],
            'price'       => ['required'],
        ],
        ValidatorInterface::RULE_UPDATE        => [
            'name'        => ['required'],
            'description' => [],
        ],
        ValidatorInterface::BULK_UPDATE_STATUS => [
            'ids'    => ['required'],
            'status' => ['required'],
        ],
        ValidatorInterface::UPDATE_STATUS_ITEM => [
            'status' => ['required'],
        ],
        "RULE_ADMIN_UPDATE_DATE"               => [
            'published_date' => ['required'],
        ],
        "RULE_IDS"                             => [
            'ids'  => ['array', 'required'],
            'ids*' => ['integer'],
        ],
    ];

    public function getSchemaRules($entity)
    {
        $schema = collect($entity->schema());

        $rules = $schema->map(function ($item) {
            return $item['rule'];
        });

        return $rules->toArray();
    }

    public function getNoRuleFields($entity)
    {

        $schema = collect($entity->schema());

        $fields = $schema->filter(function ($item) {
            return count($item['rule']) === 0;
        });

        return $fields->toArray();
    }

    public function isSchemaValid($data, $rules)
    {
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            throw new Exception($validator->errors(), 1000);
        }
        return true;
    }
}
