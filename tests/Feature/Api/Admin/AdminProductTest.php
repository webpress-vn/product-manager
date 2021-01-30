<?php

namespace VCComponent\Laravel\Product\Test\Feature\Api\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Product\Entities\Product;
use VCComponent\Laravel\Product\Entities\ProductSchema;
use VCComponent\Laravel\Product\Test\Stubs\Models\Product as TestEntity;
use VCComponent\Laravel\Product\Test\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_get_list_products_with_paginate_by_admin_router()
    {
        $number       = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);
        }

        $response = $this->call('GET', 'api/product-management/admin/products');

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

    /**
     * @test
     */
    public function can_show_list_product_by_admin_router()
    {
        $number       = rand(1, 5);
        $listProducts = [];
        for ($i = 0; $i < $number; $i++) {
            $product = factory(Product::class)->create()->toArray();
            unset($product['updated_at']);
            unset($product['created_at']);
            array_push($listProducts, $product);

        }

        $response = $this->call('GET', 'api/product-management/admin/products/all');

        $response->assertStatus(200);

        /* sort by id */
        $listIds = array_column($listProducts, 'id');
        array_multisort($listIds, SORT_DESC, $listProducts);

        $response->assertJson(['data' => $listProducts]);

    }

    /** @test */
    public function can_show_product_by_id_admin_router()
    {
        $product = factory(Product::class)->create();

        $response = $this->json('GET', 'api/product-management/admin/products/' . $product->id);

        $data = $product->toArray();
        unset($data['updated_at']);
        unset($data['created_at']);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('products', $data);
    }

    /** @test */
    public function can_create_product_by_admin_router()
    {
        $data = factory(Product::class)->make()->toArray();

        $response = $this->json('POST', 'api/product-management/admin/products', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('products', $data);
    }

    /**
     * @test
     */
    public function can_update_product_by_admin_router()
    {
        $product = factory(Product::class)->create();

        $id            = $product->id;
        $product->name = 'update name';
        $data          = $product->toArray();
        $response      = $this->json('PUT', 'api/product-management/admin/products/' . $id, $data);

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

    /**
     * @test
     */
    public function can_get_field_meta_product_by_admin()
    {
        factory(ProductSchema::class)->create();

        $response = $this->json('GET', 'api/product-management/admin/products/field-meta');
        $response->assertStatus(200);

        $schemas = ProductSchema::get()->map(function ($item) {
            return [
                'id'             => $item->id,
                'name'           => $item->name,
                'label'          => $item->label,
                'schema_type_id' => $item->schema_type_id,
                'schema_rule_id' => $item->schema_rule_id,
                'product_type'   => $item->product_type,
                'timestamps'     => [
                    'created_at' => $item->created_at->toJSON(),
                    'updated_at' => $item->updated_at->toJSON(),
                ],
            ];
        })->toArray();

        $response->assertJson([
            'data' => $schemas,
        ]);
    }

    /**
     * @test
     */
    public function can_bulk_update_status_products_by_admin()
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

        $response = $this->json('GET', 'api/product-management/admin/products/all');
        $response->assertJsonFragment(['status' => 1]);

        $response = $this->json('PUT', 'api/product-management/admin/products/status/bulk', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/product-management/admin/products/all');
        $response->assertJsonFragment(['status' => 5]);
    }

    /**
     * @test
     */
    public function can_update_status_a_product_by_admin()
    {
        $product = factory(Product::class)->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $data     = ['status' => 2];
        $response = $this->json('PUT', 'api/product-management/admin/product/' . $product['id'] . '/status', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/product-management/admin/products/' . $product['id']);

        $response->assertJson(['data' => $data]);
    }

    /**
     * @test
     */
    public function can_change_published_date_a_product_by_admin()
    {
        $product = factory(Product::class)->create()->toArray();

        $data     = ['published_date' => date('Y-m-d', strtotime('20-10-2020'))];
        $response = $this->json('PUT', 'api/product-management/admin/product/' . $product['id'] . '/date', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
    }

    /**
     * @test
     */
    public function can_check_stock_a_product_by_admin()
    {
        $product = factory(Product::class)->create()->toArray();

        $response = $this->json('GET', 'api/product-management/admin/product/' . $product['id'] . '/stock');

        $response->assertStatus(200);
        $response->assertJson(['in_stock' => true]);

        $product  = factory(Product::class)->make(['quantity' => 0])->toArray();
        $response = $this->json('POST', 'api/product-management/admin/products', $product);

        $productId = $response->decodeResponseJson()['data']['id'];
        $response  = $this->json('GET', 'api/product-management/admin/product/' . $productId . '/stock');
        $response->assertJson(['in_stock' => false]);
    }

    /**
     * @test
     */
    public function can_update_quantity_a_product_by_admin()
    {
        $product = factory(Product::class)->create()->toArray();

        $number   = rand(1, 1000);
        $data     = ['quantity' => $number];
        $response = $this->json('PUT', 'api/product-management/admin/product/' . $product['id'] . '/quantity', $data);

        $response->assertJson(['quantity' => $data['quantity'] + $product['quantity']]);
    }

    /**
     * @test
     */
    public function can_change_quantity_a_product_by_admin()
    {
        $product = factory(Product::class)->create()->toArray();

        $number   = rand(1, 1000);
        $data     = ['quantity' => $number];
        $response = $this->json('PUT', 'api/product-management/admin/product/' . $product['id'] . '/change_quantity', $data);
        $response->assertJson(['quantity' => $data['quantity']]);
    }

    /**
     * @test
     */
    public function can_get_product_type_by_admin()
    {
        $response = $this->json('GET', 'api/product-management/admin/productTypes');

        $entity          = new TestEntity;
        $getProductTypes = $entity->productTypes();
        $response->assertJson([
            'data' => $getProductTypes,
        ]);
    }

    /**
     * @test
     */
    public function can_export_product_by_admin_router()
    {
        $product = factory(Product::class)->create();

        $data  = [$product];
        $param = '?label=product&extension=xlsx';

        $response = $this->call('GET', 'api/product-management/admin/products/exports' . $param);
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJson(['data' => [[
            "Tên sản phẩm"    => $product->name,
            "Số lượng"        => $product->quantity,
            "Số lượng đã bán" => $product->sold_quantity,
            "Mã sản phẩm"     => $product->code,
            "Link ảnh"        => $product->thumbnail,
            "Gía bán"         => $product->price,
            "Đơn vị tính"     => $product->unit_price,
        ]]]);
    }
}
