<?php

namespace VCComponent\Laravel\Product\Products\Contracts;

use VCComponent\Laravel\Product\Repositories\ProductRepository;

interface Product
{

    public function _contract();

    public function getHotProducts($number);
    public function getModel();
}
