<?php

namespace VCComponent\Laravel\Product\Test\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Product\Test\TestCase;
use Illuminate\Support\Facades\App;
use VCComponent\Laravel\Product\Entities\Product;
use VCComponent\Laravel\Category\Entities\Category;
use VCComponent\Laravel\Category\Entities\Categoryable;
use VCComponent\Laravel\Product\Repositories\ProductRepository;
class ProductReponsitoryTest extends TestCase
{
    use RefreshDatabase;


    /**
     * @test
     */

    public function can_get_product_url()
    {
        $repository = App::make(ProductRepository::class);
        $product_a  = factory(Product::class)->create(['name'=>'a']);
        $product_b  = factory(Product::class)->create(['name'=>'b']);
        $this->assertSame('/products/'.$product_a->slug, $repository->getProductUrl($product_a->id));
        $this->assertSame('/products/'.$product_b->slug, $repository->getProductUrl($product_b->id));
    }

    /**
     * @test
     */

    public function can_get_product_by_id()
    {
        $repository = App::make(ProductRepository::class);
        $product_a  = factory(Product::class)->create(['name'=>'a']);
        $product_b  = factory(Product::class)->create(['name'=>'b']);
        $this->assertSame('a', $repository->getProductByID($product_a->id)->name);
        $this->assertSame('b', $repository->getProductByID($product_b->id)->name);
    }

    /**
     * @test
     */

    public function can_find_by_where()
    {
        $repository = App::make(ProductRepository::class);
        factory(Product::class)->create(['name'=>'a']);
        factory(Product::class)->create(['name'=>'b']);
        $this->assertSame('a', $repository->findByWhere(['name'=>'a'])[0]->name);
    }


    /**
     * @test
     */

    public function can_find_product_by_field()
    {
        $repository = App::make(ProductRepository::class);
        factory(Product::class)->create(['name'=>'a']);
        factory(Product::class)->create(['name'=>'b']);
        $this->assertSame('a', $repository->findProductByField('name','a')[0]->name);
    }

    /**
     * @test
     */

    public function can_get_search_result_paginate()
    {
        $repository = App::make(ProductRepository::class);
        factory(Product::class)->create(['name'=>'a test function result','description'=>'test']);
        factory(Product::class)->create(['name'=>'b test function','description'=>'test']);
        factory(Product::class)->create(['name'=>'c test function','description'=>'result']);
        $this->assertSame('a test function result', $repository->getSearchResultPaginate('result',['name','description'])[0]->name);
        $this->assertSame('c test function', $repository->getSearchResultPaginate('result',['name','description'])[1]->name);
    }

     /**
     * @test
     */

    public function can_get_search_result()
    {
        $repository = App::make(ProductRepository::class);
        factory(Product::class)->create(['name'=>'a test function result','description'=>'test']);
        factory(Product::class)->create(['name'=>'b test function','description'=>'test']);
        factory(Product::class)->create(['name'=>'c test function','description'=>'result']);
        $this->assertSame('a test function result', $repository->getSearchResult('result',['name','description'])[0]->name);
        $this->assertSame('c test function', $repository->getSearchResult('result',['name','description'])[1]->name);
    }

    /**
     * @test
     */

    public function can_get_products_with_category_paginate()
    {
        $repository = App::make(ProductRepository::class);
        Category::create(['name'=>'a category','type' =>'products']);
        Category::create(['name'=>'b category','type' =>'products']);
        factory(Product::class)->create(['name'=>'a test function']);
        factory(Product::class)->create(['name'=>'b test function']);
        factory(Product::class)->create(['name'=>'c test function']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>1,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>2,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'2','categoryable_id'=>3,'categoryable_type' => 'products']);
        $this->assertSame('a test function', $repository->getProductsWithCategoryPaginate(1)[0]->name);
        $this->assertSame('c test function', $repository->getProductsWithCategoryPaginate(2)[0]->name);
    }
    /**
     * @test
     */
    public function can_get_products_with_category()
    {
        $repository = App::make(ProductRepository::class);
        Category::create(['name'=>'a category','type' =>'products']);
        Category::create(['name'=>'b category','type' =>'products']);
        factory(Product::class)->create(['name'=>'a test function']);
        factory(Product::class)->create(['name'=>'b test function']);
        factory(Product::class)->create(['name'=>'c test function']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>1,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>2,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'2','categoryable_id'=>3,'categoryable_type' => 'products']);
        $this->assertSame('a test function', $repository->getProductsWithCategory(1)[0]->name);
        $this->assertSame('c test function', $repository->getProductsWithCategory(2)[0]->name);
    }

    /**
     * @test
     */
    public function can_get_related_products_paginate()
    {
        $repository = App::make(ProductRepository::class);
        Category::create(['name'=>'a category','type' =>'products']);
        Category::create(['name'=>'b category','type' =>'products']);
        factory(Product::class)->create(['name'=>'a test function']);
        factory(Product::class)->create(['name'=>'b test function']);
        factory(Product::class)->create(['name'=>'c test function']);
        factory(Product::class)->create(['name'=>'d test function']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>1,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>2,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>3,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'2','categoryable_id'=>4,'categoryable_type' => 'products']);
        $this->assertSame('b test function', $repository->getRelatedProductsPaginate(1)[0]->name);
        $this->assertSame('c test function', $repository->getRelatedProductsPaginate(1)[1]->name);
    }

    /**
     * @test
     */
    public function can_get_related_products()
    {
        $repository = App::make(ProductRepository::class);
        Category::create(['name'=>'a category','type' =>'products']);
        Category::create(['name'=>'b category','type' =>'products']);
        factory(Product::class)->create(['name'=>'a test function']);
        factory(Product::class)->create(['name'=>'b test function']);
        factory(Product::class)->create(['name'=>'c test function']);
        factory(Product::class)->create(['name'=>'d test function']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>1,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>2,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'1','categoryable_id'=>3,'categoryable_type' => 'products']);
        Categoryable::create(['category_id'=>'2','categoryable_id'=>4,'categoryable_type' => 'products']);
        $this->assertSame('b test function', $repository->getRelatedProducts(1)[0]->name);
        $this->assertSame('c test function', $repository->getRelatedProducts(1)[1]->name);
    }





}
