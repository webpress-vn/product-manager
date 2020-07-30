<?php

namespace VCComponent\Laravel\Product\Traits;

use Illuminate\Http\Request;
use VCComponent\Laravel\Product\Entities\Variant;

trait HasVariantTrait
{
    public function variants()
    {
        return $this->morphedByMany(Variant::class, 'variantable');
    }

    public function attachVariants($variant_ids)
    {
        $this->variants()->attach($variant_ids);
    }

}
