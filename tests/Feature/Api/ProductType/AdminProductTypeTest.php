<?php

namespace VCComponent\Laravel\Product\Test\Feature\Api\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Product\Entities\Product;
use VCComponent\Laravel\Product\Entities\ProductSchema;
use VCComponent\Laravel\Product\Test\Stubs\Models\Product as Entity;
use VCComponent\Laravel\Product\Test\TestCase;

class AdminProductTypeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_get_field_meta_product_type_by_admin_router()
    {
        factory(ProductSchema::class)->create(['product_type' => 'sim']);

        $response = $this->call('GET', 'api/product-management/admin/sim/field-meta');

        $schemas = ProductSchema::where('product_type', 'sim')->get()->map(function ($item) {
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

        $response->assertStatus(200);
        $response->assertJson(['data' => $schemas]);
    }

    /**
     * @test
     */
    public function can_bulk_force_delete_products_by_admin_with_type()
    {
        $products = factory(Product::class, 5)->state('sim')->create();

        $products = $products->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($products, 'id');
        $data    = ["ids" => $listIds];

        $response = $this->call('DELETE', 'api/product-management/admin/sim/force/bulk', $data);

        $response->assertJson(['success' => true]);

        foreach ($products as $item) {
            $this->assertDeleted('products', $item);
        }
    }

    /**
     * @test
     */
    public function can_bulk_soft_products_to_trash_by_admin_router_with_type()
    {
        $products = factory(Product::class, 5)->state('sim')->create();

        $products = $products->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($products, 'id');
        $data    = ["ids" => $listIds];

        $response = $this->call('DELETE', 'api/product-management/admin/sim/bulk', $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        foreach ($products as $item) {
            $this->assertSoftDeleted('products', $item);
        }
    }

    /**
     * @test
     */
    public function can_force_delete_a_product_by_admin_with_type()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();

        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->call('DELETE', 'api/product-management/admin/sim/' . $product['id'] . '/force');

        $response->assertJson(['success' => true]);
        $this->assertDeleted('products', $product);
    }

    /**
     * @test
     */
    public function can_delete_all_trash_product_type_by_admin_router()
    {
        $products = factory(Product::class, 5)->state('sim')->create();

        $products = $products->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($products, 'id');
        $data    = ["ids" => $listIds];

        $response = $this->call('DELETE', 'api/product-management/admin/sim/bulk', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->call('GET', 'api/product-management/admin/sim/trash/all');

        $response->assertJsonCount(5, 'data');

        $response = $this->call('DELETE', 'api/product-management/admin/sim/trash/all');
        $response->assertJson(['success' => true]);

        foreach ($products as $item) {
            $this->assertDeleted('products', $item);
        }
    }

    /**
     * @test
     */
    public function can_bulk_delete_products_type_trash_by_admin()
    {
        $products = factory(Product::class, 5)->state('sim')->create();

        $products = $products->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($products, 'id');
        $data    = ["ids" => $listIds];

        $response = $this->call('DELETE', 'api/product-management/admin/sim/trash/bulk', $data);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Product not found']);

        $response = $this->call('DELETE', 'api/product-management/admin/sim/bulk', $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->call('GET', 'api/product-management/admin/sim/trash/all');
        $response->assertJsonCount(5, 'data');

        $response = $this->call('DELETE', 'api/product-management/admin/sim/trash/bulk', $data);
        $response->assertJson(['success' => true]);

        foreach ($products as $item) {
            $this->assertDeleted('products', $item);
        }
    }

    /**
     * @test
     */
    public function can_delete_a_products_type_trash_by_admin()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();

        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->json('DELETE', 'api/product-management/admin/sim/trash/' . $product['id']);
        $response->assertJson(['message' => 'Product not found']);

        $response = $this->call('DELETE', 'api/product-management/admin/sim/' . $product['id']);

        $this->assertSoftDeleted('products', $product);

        $response = $this->json('DELETE', 'api/product-management/admin/sim/trash/' . $product['id']);
        $response->assertJson(['success' => true]);

        $this->assertDeleted('products', $product);
    }

    /**
     * @test
     */
    public function can_get_trash_list_of_products_type_with_no_paginate_by_admin()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $response = $this->call('DELETE', 'api/product-management/admin/sim/' . $product['id']);
        $response->assertJson(['success' => true]);

        $response = $this->call('GET', 'api/product-management/admin/sim/trash/all');
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
    public function can_get_trash_list_of_products_type_with_paginate_by_admin()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $response = $this->call('DELETE', 'api/product-management/admin/sim/' . $product['id']);
        $response->assertJson(['success' => true]);

        $response = $this->call('GET', 'api/product-management/admin/sim/trash');
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
    public function can_bulk_restore_products_type_by_admin_router()
    {
        $products = factory(Product::class, 5)->state('sim')->create();

        $products = $products->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($products, 'id');
        $data    = ["ids" => $listIds];

        $response = $this->call('DELETE', 'api/product-management/admin/sim/bulk', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        foreach ($products as $item) {
            $this->assertSoftDeleted('products', $item);
        }

        $response = $this->call('PUT', 'api/product-management/admin/products/trash/bulk/restores', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        foreach ($products as $item) {
            $response = $this->call('GET', 'api/product-management/admin/products/' . $item['id']);
            $response->assertStatus(200);
            $response->assertJson(['data' => $item]);
        }
    }

    /**
     * @test
     */
    public function can_restore_a_product_type_by_admin_router()
    {

        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->call('DELETE', 'api/product-management/admin/sim/' . $product['id']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertSoftDeleted('products', $product);

        $response = $this->call('PUT', 'api/product-management/admin/sim/trash/' . $product['id'] . '/restore');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->call('GET', 'api/product-management/admin/sim/' . $product['id']);
        $response->assertStatus(200);
        $response->assertJson(['data' => $product]);
    }

    /**
     * @test
     */
    public function can_get_list_of_products_type_with_no_paginate_by_admin()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->call('GET', 'api/product-management/admin/sim/all');
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
    public function can_bulk_update_status_products_type_by_admin()
    {
        $products = factory(Product::class, 5)->state('sim')->create();

        $products = $products->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($products, 'id');
        $data    = ['ids' => $listIds, 'status' => 5];

        $response = $this->json('GET', 'api/product-management/admin/sim/all');
        $response->assertJsonFragment(['status' => 1]);

        $response = $this->json('PUT', 'api/product-management/admin/sim/status/bulk', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/product-management/admin/sim/all');
        $response->assertJsonFragment(['status' => 5]);
    }

    /**
     * @test
     */
    public function can_update_status_a_product_type_by_admin()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $data     = ['status' => 2];
        $response = $this->json('PUT', 'api/product-management/admin/sim/' . $product['id'] . '/status', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/product-management/admin/sim/' . $product['id']);

        $response->assertJson(['data' => $data]);
    }

    /**
     * @test
     */
    public function can_create_sim_product_type_by_admin_router()
    {
        $data = factory(Product::class)->state('sim')->make()->toArray();

        $response = $this->json('POST', 'api/product-management/admin/sim', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('products', $data);
    }

    /**
     * @test
     */
    public function can_update_sim_product_type_by_admin_router()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();

        $id              = $product['id'];
        $product['name'] = 'update name';
        $data            = $product;

        unset($data['updated_at']);
        unset($data['created_at']);

        $response = $this->json('PUT', 'api/product-management/admin/sim/' . $id, $data);

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
    public function can_delete_sim_product_type_by_admin_router()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();

        unset($product['updated_at']);
        unset($product['created_at']);

        $this->assertDatabaseHas('products', $product);

        $response = $this->call('DELETE', 'api/product-management/admin/sim/' . $product['id']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertSoftDeleted('products', $product);
    }

    /**
     * @test
     */
    public function can_get_product_type_item_by_admin_router()
    {
        $product = factory(Product::class)->state('sim')->create();

        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->call('GET', 'api/product-management/admin/sim/' . $product->id);

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
    public function can_get_product_type_list_by_admin_router()
    {
        $products = factory(Product::class, 5)->state('sim')->create();

        $response = $this->call('GET', 'api/product-management/admin/sim');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    /**
     * @test
     */
    public function can_change_published_date_a_product_type_by_admin()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();

        $data     = ['published_date' => date('Y-m-d', strtotime('20-10-2020'))];
        $response = $this->json('PUT', 'api/product-management/admin/sim/' . $product['id'] . '/date', $data);

        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
    }

    /**
     * @test
     */
    public function can_check_stock_a_product_by_admin()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();

        $response = $this->json('GET', 'api/product-management/admin/sim/' . $product['id'] . '/stock');

        $response->assertStatus(200);
        $response->assertJson(['in_stock' => true]);

        $product  = factory(Product::class)->make(['quantity' => 0])->toArray();
        $response = $this->json('POST', 'api/product-management/admin/sim', $product);

        $productId = $response->decodeResponseJson()['data']['id'];
        $response  = $this->json('GET', 'api/product-management/admin/sim/' . $productId . '/stock');
        $response->assertJson(['in_stock' => false]);
    }

    /**
     * @test
     */
    public function can_update_quantity_a_product_type_by_admin()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();

        $number   = rand(1, 1000);
        $data     = ['quantity' => $number];
        $response = $this->json('PUT', 'api/product-management/admin/sim/' . $product['id'] . '/quantity', $data);

        $response->assertJson(['quantity' => $data['quantity'] + $product['quantity']]);
    }

    /**
     * @test
     */
    public function can_change_quantity_a_product_type_by_admin()
    {
        $product = factory(Product::class)->state('sim')->create()->toArray();

        $number   = rand(1, 1000);
        $data     = ['quantity' => $number];
        $response = $this->json('PUT', 'api/product-management/admin/sim/' . $product['id'] . '/change_quantity', $data);
        $response->assertJson(['quantity' => $data['quantity']]);
    }

    /**
     * @test
     */
    public function can_export_product_type_by_admin_router()
    {
        $product = factory(Product::class)->state('sim')->create();

        $data  = [$product];
        $param = '?label=product&extension=xlsx';

        $response = $this->call('GET', 'api/product-management/admin/sim/exports' . $param);
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
