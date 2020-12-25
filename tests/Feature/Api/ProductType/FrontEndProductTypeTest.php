<?php

namespace VCComponent\Laravel\Product\Test\Feature\Api\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Product\Entities\Product;
use VCComponent\Laravel\Product\Test\Stubs\Models\Product as Entity;
use VCComponent\Laravel\Product\Test\TestCase;

class FrontEndProductTypeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_get_list_of_products_type_with_no_paginate_by_frontend_router()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->call('GET', 'api/product-management/sim/all');
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
    public function can_bulk_update_status_products_type_by_frontend_router()
    {
        $products = factory(Product::class, 5)->state('sim')->create();

        $products = $products->map(function($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();
        
        $listIds = array_column($products, 'id');
        $data    = ['ids' => $listIds, 'status' => 5];

        $response = $this->json('GET', 'api/product-management/sim/all');
        $response->assertJsonFragment(['status' => 1]);

        $response = $this->json('PUT', 'api/product-management/sim/status/bulk', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/product-management/sim/all');
        $response->assertJsonFragment(['status' => 5]);
    }

    /**
     * @test
     */
    public function can_update_status_a_product_type_by_frontend_router()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $data     = ['status' => 2];
        $response = $this->json('PUT', 'api/product-management/sim/' . $product['id'] . '/status', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/product-management/sim/' . $product['id']);

        $response->assertJson(['data' => $data]);
    }

    /**
     * @test
     */
    public function can_create_sim_product_type_by_frontend_router()
    {
        $data = factory(Product::class)->state('sim')->make()->toArray();

        $response = $this->json('POST', 'api/product-management/sim', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('products', $data);
    }

    /**
     * @test
     */
    public function can_update_sim_product_type_by_frontend_router()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();

        $id              = $product['id'];
        $product['name'] = 'update name';
        $data            = $product;

        unset($data['updated_at']);
        unset($data['created_at']);

        $response = $this->json('PUT', 'api/product-management/sim/' . $id, $data);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name' => $data['name'],
            ],
        ]);

        $this->assertDatabaseHas('products', $data);
    }

    /**
     * @test
     */
    public function can_delete_sim_product_type_by_frontend_router()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();

        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $response = $this->call('DELETE', 'api/product-management/sim/' . $product['id']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertSoftDeleted('products', $product);
    }

    /**
     * @test
     */
    public function can_get_product_type_item_by_frontend_router()
    {
        $product = factory(Product::class)->state('sim')->create();

        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->call('GET', 'api/product-management/sim/' . $product->id);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name'         => $product->name,
                'description'  => $product->description,
                'product_type' => 'sim',
            ],
        ]);
    }

    /**
     * @test
     */
    public function can_get_product_type_list_by_frontend_router()
    {
        $products = factory(Product::class, 5)->state('sim')->create();

        $response = $this->call('GET', 'api/product-management/sim');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }
}
