<?php

namespace VCComponent\Laravel\Product\Products\Contracts;

interface Product
{

    public function __construct();

    public function getHotProducts($number);
}
