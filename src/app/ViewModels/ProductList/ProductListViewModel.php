<?php

namespace VCComponent\Laravel\Product\ViewModels\ProductList;

use Carbon\Carbon;
use Illuminate\Support\Str;
use VCComponent\Laravel\ViewModel\ViewModels\BaseViewModel;

class ProductListViewModel extends BaseViewModel
{
    public $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function getDisplayDatetimeAttribute($item)
    {

        return Carbon::parse($item->created_at)->format('d-m-Y h:i:s A');
    }

    public function getLimitDescription($limit = 30)
    {

        return Str::limit($this->products->description, $limit);
    }

    public function getLimitedName($limit = 10)
    {
        return Str::limit($this->products->name, $limit);
    }
}
