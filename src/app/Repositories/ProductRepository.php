<?php

namespace VCComponent\Laravel\Product\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ProductRepository.
 *
 * @package namespace VCComponent\Laravel\Product\Repositories;
 */
interface ProductRepository extends RepositoryInterface
{
    public function getWithPagination($filters, $type);
    public function getStock($query);
    public function getMaxId();
    public function getOutStock($query);
    public function bulkUpdateStatus($request);

    public function getRelatedProducts($product_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc');
    public function getRelatedProductsPaginate($product_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc');
    public function getProductsWithCategory($category_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc', $columns = ['*']);
    public function getProductsWithCategoryPaginate($category_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc', $columns = ['*']);
    public function getSearchResult($key_word,array $list_field  = ['name'], array $where = [], $category_id = 0,$number = 10,$order_by = 'order', $order = 'asc', $columns = ['*']);
    public function getSearchResultPaginate($key_word, array $list_field  = ['name'], array $where = [], $category_id = 0,$number = 10,$order_by = 'order', $order = 'asc', $columns = ['*']);
    public function findProductByField($field, $value);
    public function findByWhere(array $where);
    public function getProductByID($product_id);
    public function getProductMedias($product_id, $image_dimension= '');
    public function getProductUrl($product_id);
}
