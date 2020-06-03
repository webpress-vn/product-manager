<?php

namespace VCComponent\Laravel\Product\Http\Controllers\Web;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use VCComponent\Laravel\Product\Contracts\ViewProductDetailControllerInterface;
use VCComponent\Laravel\Product\Repositories\ProductRepository;
use VCComponent\Laravel\Product\ViewModels\ProductDetail\ProductDetailViewModel;

class ProductDetailController extends Controller implements ViewProductDetailControllerInterface
{
    protected $repository;
    protected $entity;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
        $this->entity     = $repository->getEntity();

        if (isset(config('post.viewModels')['productDetail'])) {
            $this->ViewModel = config('post.viewModels.productDetail');
        } else {
            $this->ViewModel = ProductDetailViewModel::class;
        }
    }

    public function show($slug, Request $request)
    {
        if (method_exists($this, 'beforeQuery')) {
            $this->beforeQuery($request);
        }

        $product = $this->entity->findBySlugOrFail($slug);

        if (method_exists($this, 'afterQuery')) {
            $this->afterQuery($product, $request);
        }

        $view_model = new $this->ViewModel($product);

        $custom_view_data = $this->viewData($product, $request);
        $data             = array_merge($custom_view_data, $view_model->toArray());

        if (method_exists($this, 'beforeView')) {
            $this->beforeView($data, $request);
        }

        return view($this->view(), $data);
    }

    protected function view()
    {
        return 'product-manager::product-detail';
    }

    protected function viewData($products, Request $request)
    {
        return [];
    }
}
