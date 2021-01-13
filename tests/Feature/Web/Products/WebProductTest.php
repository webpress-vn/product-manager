<?php

namespace VCComponent\Laravel\Product\Test\Feature\Web\Products;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Product\Entities\Product;
use VCComponent\Laravel\Product\Test\Stubs\Models\Product as TestEntity;
use VCComponent\Laravel\Product\Test\TestCase;

class WebProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_get_list_products_by_web_router()
    {
        $products = factory(Product::class)->create()->toArray();
        unset($products['updated_at']);
        unset($products['created_at']);

        $response = $this->call('GET', 'product-management/products');

        $response->assertStatus(200);
        $response->assertViewIs("product-manager::product-list");
    }

    /**
     * @test
     */
    public function can_get_a_product_by_web_router()
    {

        $product = factory(Product::class)->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->call('GET', 'product-management/products/' . $product['slug']);

        $response->assertStatus(200);
        $response->assertViewIs("product-manager::product-detail");
        $response->assertViewHasAll([
            'product.name'     => $product['name'],
            'product.slug'     => $product['slug'],
            'product.quantity' => $product['quantity'],
            'product.price'    => $product['price'],
        ]);
    }


    /**
     * @test
     */
    public function can_get_list_products_type_by_web_router()
    {

        $products = factory(Product::class)->state('sim')->create()->toArray();
        unset($products['updated_at']);
        unset($products['created_at']);

        $response = $this->call('GET', 'product-management/sim');

        $response->assertStatus(200);
        $response->assertViewIs("product-manager::product-list");
    }

    /**
     * @test
     */
    public function can_get_a_product_type_by_web_router()
    {

        $product = factory(Product::class)->state('sim')->create()->toArray();
        unset($product['updated_at']);
        unset($product['created_at']);

        $response = $this->call('GET', 'product-management/sim/' . $product['slug']);

        $response->assertStatus(200);
        $response->assertViewIs("product-manager::product-detail");
        $response->assertViewHasAll([
            'product.name'     => $product['name'],
            'product.slug'     => $product['slug'],
            'product.quantity' => $product['quantity'],
            'product.price'    => $product['price'],
        ]);
    }
}
