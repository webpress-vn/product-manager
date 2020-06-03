<?php

namespace VCComponent\Laravel\Product\ViewModels\ProductDetail;

use Carbon\Carbon;
use Illuminate\Support\Str;
use VCComponent\Laravel\ViewModel\ViewModels\BaseViewModel;

class ProductDetailViewModel extends BaseViewModel
{
    public $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function getDisplayDatetimeAttribute()
    {

        return Carbon::parse($this->product->created_at)->format('d-m-Y h:i:s A');
    }

    public function getLimitDescription($limit = 30)
    {

        return Str::limit($this->product->description, $limit);
    }

    public function getLimitedName($limit = 10)
    {
        return Str::limit($this->product->name, $limit);
    }

    public function isAvailable()
    {
        if ($this->product->quantity) {
            return true;
        }
        return false;
    }
}
