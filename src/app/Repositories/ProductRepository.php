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
    public function getWithPagination($filters);
    public function getStock($query);
    public function getMaxId();
    public function getOutStock($query);
    public function bulkUpdateStatus($request);
}
