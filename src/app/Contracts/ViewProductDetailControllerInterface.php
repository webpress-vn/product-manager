<?php

namespace VCComponent\Laravel\Product\Contracts;

use Illuminate\Http\Request;

interface ViewProductDetailControllerInterface
{
    public function show($id, Request $request);
}
