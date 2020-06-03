<?php

namespace VCComponent\Laravel\Product\Repositories;

use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\App;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use VCComponent\Laravel\Product\Entities\Product;
use VCComponent\Laravel\Product\Repositories\ProductRepository;
use VCComponent\Laravel\Vicoders\Core\Exceptions\NotFoundException;

/**
 * Class ProductRepositoryEloquent.
 *
 * @package namespace VCComponent\Laravel\Product\Repositories;
 */
class ProductRepositoryEloquent extends BaseRepository implements ProductRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        if (isset(config('product.models')['product'])) {
            return config('product.models.product');
        } else {
            return Product::class;
        }
    }

    public function getEntity()
    {
        return $this->model;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getWithPagination($filters)
    {
        $request = App::make(Request::class);
        $query   = $this->getEntity();

        $items = App::make(Pipeline::class)
            ->send($query)
            ->through($filters)
            ->then(function ($content) use ($request) {
                $per_page = $request->has('per_page') ? (int) $request->get('per_page') : 15;
                $products = $content->paginate($per_page);
                return $products;
            });

        return $items;
    }

    public function getMaxId()
    {
        $query = $this->getEntity();

        $max_id = $query->max('id');

        return ($max_id);
    }

    public function getStock($query)
    {
        $stock = $query->where('quantity', '>', 0);
        return $stock;
    }

    public function getOutStock($query)
    {
        $out_stock = $query->where('quantity', '<=', 0);
        return $out_stock;
    }

    public function checkSku($sku)
    {
        $check_sku = $this->getEntity()->where('sku', $sku)->get();
        return count($check_sku);
    }

    public function bulkUpdateStatus($request)
    {

        $data  = $request->all();
        $posts = $this->findWhereIn("id", $request->ids);

        if (count($request->ids) > $posts->count()) {
            throw new NotFoundException("Products");
        }

        $result = $this->whereIn("id", $request->ids)->update(['status' => $data['status']]);

        return $result;
    }

}
