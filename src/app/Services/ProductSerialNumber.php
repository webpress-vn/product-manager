<?php

namespace VCComponent\Laravel\Product\Services;

use VCComponent\Laravel\Product\Repositories\ProductRepository;

class ProductSerialNumber
{
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        $this->productEntity = $productRepository->getEntity();
    }
    public function productSerialNumberCreate($product)
    {
        $type = $product->product_type;
        $productTypeMax = $this->productEntity->where('product_type', $type)->orderBy('product_type_serial_number', 'DESC')->first();
        $productTypeSerialNumberMax = $productTypeMax->product_type_serial_number;
        $product['product_type_serial_number'] =  $productTypeSerialNumberMax + 1;
        $product->save();
    }
}
