<?php

namespace VCComponent\Laravel\Product\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use VCComponent\Laravel\Product\Contracts\ViewProductListControllerInterface;
use VCComponent\Laravel\Product\Pipes\ApplyConstraints;
use VCComponent\Laravel\Product\Pipes\ApplyOrderBy;
use VCComponent\Laravel\Product\Pipes\ApplySearch;
use VCComponent\Laravel\Product\Repositories\ProductRepository;
use VCComponent\Laravel\Product\Traits\Helpers;
use VCComponent\Laravel\Product\ViewModels\ProductList\ProductListViewModel;

class ProductListController extends Controller implements ViewProductListControllerInterface
{
    use Helpers;

    protected $repository;
    protected $entity;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
        $this->entity     = $repository->getEntity();

        if (isset(config('post.viewModels')['productList'])) {
            $this->ViewModel = config('post.viewModels.productList');
        } else {
            $this->ViewModel = ProductListViewModel::class;
        }
    }

    public function index(Request $request)
    {
        if (method_exists($this, 'beforeQuery')) {
            $this->beforeQuery($request);
        }

        $type     = $this->getTypeProduct($request);
        $pipes    = $this->pipes();
        $products = $this->repository->getWithPagination($pipes, $type);

        if (method_exists($this, 'afterQuery')) {
            $this->afterQuery($products, $request);
        }

        $custom_view_func_name = 'viewData' . ucwords($type);
        if (method_exists($this, $custom_view_func_name)) {
            $custom_view_data = $this->$custom_view_func_name($products, $request);
        } else {
            $custom_view_data = $this->viewData($products, $request);
        }

        $view_model = new $this->ViewModel($products);

        $custom_view_data = $this->viewData($products, $request);
        $data             = array_merge($custom_view_data, $view_model->toArray());

        if (method_exists($this, 'beforeView')) {
            $this->beforeView($data, $request);
        }

        $key = 'view' . ucwords($type);
        
        if (method_exists($this, $key)) {
            return view($this->$key(), $data);
        } else {
            return view($this->view(), $data);
        }
    }

    protected function pipes()
    {
        return [
            ApplyConstraints::class,
            ApplySearch::class,
            ApplyOrderBy::class,
        ];
    }

    protected function view()
    {
        return 'product-manager::product-list';
    }

    protected function viewData($products, Request $request)
    {
        return [];
    }
}
