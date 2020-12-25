<?php

namespace VCComponent\Laravel\Product\Test\Feature\Api\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Product\Entities\Product;
use VCComponent\Laravel\Product\Test\TestCase;

class AdminDeteleTrashTest extends TestCase
{
    use RefreshDatabase;

    /**
    * @test
    */
    public function can_remove_product_to_trash_by_admin_router()
    {
        $product = factory(Product::class)->create()->toArray();

        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $response = $this->call('DELETE', 'api/product-management/admin/products/' . $product['id']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertSoftDeleted('products', $product);
    }

    /**
     * @test
     */
    public function can_restore_a_product_by_admin_router()
    {

        $product = factory(Product::class)->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->call('DELETE', 'api/product-management/admin/products/' . $product['id']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertSoftDeleted('products', $product);

        $response = $this->call('PUT', 'api/product-management/admin/products/trash/' . $product['id'] . '/restore');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->call('GET', 'api/product-management/admin/products/' . $product['id']);
        $response->assertStatus(200);
        $response->assertJson(['data' => $product]);

    }

    /**
     * @test
     */
    public function can_bulk_move_products_to_trash_by_admin_router()
    {
        $number       = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $listIds = array_column($listProducts, 'id');
        $data    = ["ids" => $listIds];

        $response = $this->call('DELETE', 'api/product-management/admin/products/bulk', $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        foreach ($listProducts as $item) {
            $this->assertSoftDeleted('products', $item);
        }
    }

    /**
     * @test
     */
    public function can_bulk_restore_products_by_admin_router()
    {
        $number       = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $listIds  = array_column($listProducts, 'id');
        $data     = ["ids" => $listIds];
        $response = $this->call('DELETE', 'api/product-management/admin/products/bulk', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        foreach ($listProducts as $item) {
            $this->assertSoftDeleted('products', $item);
        }

        $response = $this->call('PUT', 'api/product-management/admin/products/trash/bulk/restores', $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        foreach ($listProducts as $item) {
            $response = $this->call('GET', 'api/product-management/admin/products/' . $item['id']);
            $response->assertStatus(200);
            $response->assertJson(['data' => $item]);
        }
    }

    /**
     * @test
     */
    public function can_get_trash_list_with_paginate_by_admin()
    {
        $product = factory(Product::class)->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $response = $this->call('DELETE', 'api/product-management/admin/products/' . $product['id']);
        $response->assertJson(['success' => true]);

        $response = $this->call('GET', 'api/product-management/admin/products/trash');
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
        $response->assertJson(['data' => [$product]]);
    }

    /**
     * @test
     */
    public function can_get_trash_list_with_no_paginate_by_admin()
    {
        $product = factory(Product::class)->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $response = $this->call('DELETE', 'api/product-management/admin/products/' . $product['id']);
        $response->assertJson(['success' => true]);

        $response = $this->call('GET', 'api/product-management/admin/products/trash/all');
        $response->assertJsonMissingExact([
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
        $response->assertJson(['data' => [$product]]);
    }

    /**
     * @test
     */
    public function can_delete_all_trash_list_by_admin()
    {
        $number       = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $listIds = array_column($listProducts, 'id');
        $data    = ["ids" => $listIds];

        $response = $this->call('DELETE', 'api/product-management/admin/products/bulk', $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->call('GET', 'api/product-management/admin/products/trash/all');
        $response->assertJsonCount($number, 'data');

        $response = $this->call('DELETE', 'api/product-management/admin/products/trash/all');
        $response->assertJson(['success' => true]);

        foreach ($listProducts as $item) {
            $this->assertDeleted('products', $item);
        }
    }

    /**
     * @test
     */
    public function can_bulK_delete_products_trash_by_admin()
    {
        $number       = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $listIds = array_column($listProducts, 'id');
        $data    = ["ids" => $listIds];

        $response = $this->call('DELETE', 'api/product-management/admin/products/trash/bulk', $data);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Product not found']);

        $response = $this->call('DELETE', 'api/product-management/admin/products/bulk', $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->call('GET', 'api/product-management/admin/products/trash/all');
        $response->assertJsonCount($number, 'data');

        $response = $this->call('DELETE', 'api/product-management/admin/products/trash/bulk', $data);
        $response->assertJson(['success' => true]);

        foreach ($listProducts as $item) {
            $this->assertDeleted('products', $item);
        }
    }

       /**
     * @test
     */
    public function can_delete_a_products_by_admin()
    {
        $product = factory(Product::class)->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->json('DELETE', 'api/product-management/admin/products/' . $product['id'] . '/force');

        $response->assertJson(['success' => true]);
        $this->assertDeleted('products', $product);
    }

    /**
     * @test
     */
    public function can_bulk_delete_products_by_admin()
    {
        $number       = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
            $this->assertDatabaseHas('products', $product);
        }

        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        $data = ['ids' => $listIds];

        $response = $this->json('DELETE', 'api/product-management/admin/products/force/bulk', $data);
        $response->assertJson(['success' => true]);

        foreach ($listProducts as $item) {
            $this->assertDeleted('products', $item);
        }
    }

    /**
     * @test
     */
    public function can_delete_a_product_in_trash_by_admin()
    {
        $product = factory(Product::class)->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->json('DELETE', 'api/product-management/admin/products/trash/'. $product['id']);
        $response->assertStatus(400);
        $response->assertJson(['message' => 'Product not found']);

        $response = $this->json('DELETE', 'api/product-management/admin/products/'. $product['id']);
        $this->assertSoftDeleted('products', $product);

        $response = $this->json('DELETE', 'api/product-management/admin/products/trash/'. $product['id']);
        $this->assertDeleted('products', $product);
    }
}
