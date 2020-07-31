<?php

namespace VCComponent\Laravel\Product\Traits;

use Illuminate\Http\Request;
use VCComponent\Laravel\Product\Entities\Variant;

trait HasVariantTrait
{
    public function variant()
    {
        return $this->morphTo(Variant::class);
    }
}
