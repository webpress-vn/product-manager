<?php

namespace VCComponent\Laravel\Product\Contracts;

use Illuminate\Http\Request;

interface ViewProductListControllerInterface
{
    public function index(Request $request);
}
