<?php

namespace VCComponent\Laravel\Product\Test\Feature\Api\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Product\Entities\Product;
use VCComponent\Laravel\Product\Test\Stubs\Models\Product as TestEntity;
use VCComponent\Laravel\Product\Test\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_get_list_products_frontend_router()
    {
        $number       = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->call('GET', 'api/product-management/products/all');

        $response->assertStatus(200);

        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJson(['data' => $listProducts]);
    }

    /**
     * @test
     */
    public function can_bulk_update_status_products_frontend_router()
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
        $data    = ['ids' => $listIds, 'status' => 5];

        $response = $this->json('GET', 'api/product-management/products/all');
        $response->assertJsonFragment(['status' => 1]);

        $response = $this->json('PUT', 'api/product-management/products/status/bulk', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/product-management/products/all');
        $response->assertJsonFragment(['status' => 5]);
    }

    /**
     * @test
     */
    public function can_update_status_a_product_frontend_router()
    {
        $product = factory(Product::class)->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $data     = ['status' => 2];
        $response = $this->json('PUT', 'api/product-management/product/' . $product['id'] . '/status', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/product-management/products/' . $product['id']);

        $response->assertJson(['data' => $data]);
    }

    /**
     * @test
     */
    public function can_get_list_products_with_paginate_frontend_router()
    {
        $number       = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->call('GET', 'api/product-management/products');

        $response->assertStatus(200);
        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJson(['data' => $listProducts]);

        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /** @test */
    public function can_show_product_by_id_frontend_router()
    {
        $product = factory(Product::class)->create();

        $response = $this->json('GET', 'api/product-management/products/' . $product->id);

        $data = $product->toArray();
        unset($data['updated_at']);
        unset($data['created_at']);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('products', $data);
    }

    /** @test */
    public function can_create_product_by_frondend_router()
    {
        $data = factory(Product::class)->make()->toArray();

        $response = $this->json('POST', 'api/product-management/products', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('products', $data);
    }

    /**
     * @test
     */
    public function can_update_product_by_frondend_router()
    {
        $product = factory(Product::class)->create();

        $id            = $product->id;
        $product->name = 'update name';
        $data          = $product->toArray();
        $response      = $this->json('PUT', 'api/product-management/products/' . $id, $data);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name' => $data['name'],
            ],
        ]);

        unset($data['updated_at']);
        unset($data['created_at']);

        $this->assertDatabaseHas('products', $data);
    }
}
