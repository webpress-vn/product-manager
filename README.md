# Product Manager Package for Laravel

- [Product Manager Package for Laravel](#product-manager-package-for-laravel)
  - [Installation](#installation)
    - [Composer](#composer)
    - [Service Provider](#service-provider)
    - [Config and Migration](#config-and-migration)
    - [Environment](#environment)
  - [Configuration](#configuration)
    - [URL namespace](#url-namespace)
    - [Model and Transformer](#model-and-transformer)
    - [Auth middleware](#auth-middleware)
  - [Query functions provide](#query-functions-provide)
    - [List of query functions](#list-of-query-functions)
    - [Use](#use)
    - [For example](#for-example)
  - [View](#view)
  - [Routes](#routes)


Product management package for managing product in laravel framework

## Installation

### Composer

To include the package in your project, Please run following command.

```
composer require vicoders/product_management
```

### Service Provider

In your  `config/app.php`  add the following Service Providers to the end of the  `providers`  array:

```php
'providers' => [
        ...
    VCComponent\Laravel\Product\Providers\ProductServiceProvider::class,
    VCComponent\Laravel\Product\Providers\ProductRouteProvider::class,
],
```

### Config and Migration

Run the following commands to publish configuration and migration files.

```
php artisan vendor:publish --provider="VCComponent\Laravel\Product\Providers\ProductServiceProvider"
php artisan vendor:publish --provider="Dingo\Api\Provider\LaravelServiceProvider"
php artisan vendor:publish --provider "Prettus\Repository\Providers\RepositoryServiceProvider"
```
Create tables:

```
php artisan migrate
```

### Environment

In `.env` file, we need some configuration.

```
API_PREFIX=api
API_VERSION=v1
API_NAME="Your API Name"
API_DEBUG=false
```

## Configuration

### URL namespace

To avoid duplication with your application's api endpoints, the package has a default namespace for its routes which is  `product-management`. For example:

    {{url}}/api/product-management/admin/product
You can modify the package url namespace to whatever you want by modifying the `PRODUCT_COMPONENT_NAMESPACE` variable in `.env` file.

    PRODUCT_COMPONENT_NAMESPACE="your-namespace"

### Model and Transformer

You can use your own model and transformer class by modifying the configuration file `config\product.php`

```php
'models'          => [
    'product' => App\Entities\Product::class,
],

'transformers'    => [
    'product' => App\Transformers\ProductTransformer::class,
],
```
Your `Product` model class must implements `VCComponent\Laravel\Product\Contracts\ProductSchema` and `VCComponent\Laravel\Product\Contracts\ProductManagement`

```php
<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use VCComponent\Laravel\Product\Contracts\ProductManagement;
use VCComponent\Laravel\Product\Contracts\ProductSchema;
use VCComponent\Laravel\Product\Traits\ProductManagementTrait;
use VCComponent\Laravel\Product\Traits\ProductSchemaTrait;

class Product extends Model implements Transformable, ProductSchema, ProductManagement
{
    use TransformableTrait, ProductSchemaTrait, ProductManagementTrait;

    const STATUS_PENDING = 1;
    const STATUS_ACTIVE  = 2;

    protected $fillable = [
        'name',
        'description',
        'status',
        'slug',
        'price',
    ];
}
```

### Auth middleware

Configure auth middleware in configuration file `config\product.php`

```php
'auth_middleware' => [
        'admin'    => [
            'middleware' => 'jwt.auth',
            'except'     => ['index'],
        ],
        'frontend' => [
            'middleware' => 'jwt.auth',
            'except'     => ['index'],
        ],
],
```


If your has additional fields, just add the `schema`  to the `Product` model class.

```php
public function schema()
{
    return [
        'regular_price' => [
            'type' => 'text',
            'rule' => ['nullable'],
        ]
    ];
}
```


## Query functions provide
### List of query functions
Find  By Field 
```php
public function findProductByField($field, $value)
```
Find products by condition array
```php
public function findByWhere(array $where, $number = 10, $order_by = 'order', $order = 'asc');

public function findByWherePaginate(array $where, $number = 10, $order_by = 'order', $order = 'asc')
// Find products by condition array withPaginate
```
Get product by id
```php
public function getProductByID($product_id);
```
Get product image list by size
```php
public function getProductMedias($product_id, $image_dimension= '');
```
Get product link
```php
public function getProductUrl($product_id);
```
Get related products in the same category
```php
public function getRelatedProducts($product_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc');

public function getRelatedProductsPaginate($product_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc');
// get related products of the same category with pagination
```
get products by category
```php
public function getProductsWithCategory($category_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc', $columns = ['*']);

public function getProductsWithCategoryPaginate($category_id, array $where = [], $number = 10, $order_by = 'order', $order = 'asc', $columns = ['*']);
// get products by pagination category
```
Search product by keyword
```php
public function getSearchResult($key_word,array $list_field  = ['name'], array $where = [], $category_id = 0,$number = 10,$order_by = 'order', $order = 'asc', $columns = ['*']);

public function getSearchResultPaginate($key_word, array $list_field  = ['name'], array $where = [], $category_id = 0,$number = 10,$order_by = 'order', $order = 'asc', $columns = ['*']);
// Search product by keyword with pagination
```
### Use
At controller use `PostRepository` and add function `__construct`
```php
use VCComponent\Laravel\Product\Repositories\ProductRepository;
```
```php
public function __construct(ProductRepository $productRepo) 
{
    $this->productRepo = $productRepo;
}
```
### For example
```php
$product = $this->productRepo->findProductByField('name','product hot');
// get a product named hot product

$productWhere = $this->productRepo->findByWhere(['name'=>'product hot','status'=>1]);
// get a product named hot product and status = 1

$productWhere = $this->productRepo->findByWherePaginate(['name'=>'product hot','status'=>1]);
// get a product named hot product and status = 1 with paginate

$productById = $this->productRepo->getProductByID(1);
// get product with id = 1

$productMedia = $this->productRepo->getProductMedias(2);
// get a list of images of product with id = 2

$product = $this->productRepo->getProductUrl(1);
// get the product link with id = 1

$productsRelated = $this->productRepo->getRelatedProducts(1);
// get all products in the same category as the product with id = 1

$productsRelatedPaginate = $this->productRepo->getRelatedProductsPaginate(1);
// get all products of the same category as the product with id=1 with pagination

$productsWithCategory = $this->productRepo->getProductsWithCategory(1);
// get all products in category id = 1 

$productsWithCategoryPaginate = $this->productRepo->getProductsWithCategory(1);
// get all products of category id = 2  with pagination

$productsResult = $this->productRepo->getSearchResult('hot',['name','description']);
// get all products that contain "hot" in name or description field 

$productsResult = $this->productRepo->getSearchResult('hot',['name','description'],['status'=>1],3);
// get all product that contain "hot" in name or description field and have status = 1 field and belong to category with id = 3

$productsResult = $this->productRepo->getSearchResultPaginate('hot',['name','description'],['status'=>1],3);
// get all product that contain "hot" in name or description field and have status = 1 field and belong to category with id = 3 with paginate
```

## View

Your `ProductListController` controller class must extends `VCComponent\Laravel\Product\Http\Controllers\Web\ProductListController as BaseProductListController` implements `VCComponent\Laravel\Product\Contracts\ViewProductListControllerInterface;`

```php
class ProductListController extends BaseProductListController implements ViewProductListControllerInterface
{
}
```

Your `ProductDetailController` controller class must extends `VCComponent\Laravel\Product\Http\Controllers\Web\ProductDetailController as BaseProductDetailController` implements `VCComponent\Laravel\Product\Contracts\ViewProductDetailControllerInterface;`

```php
class ProductDetailController extends BaseProductDetailController implements ViewProductDetailControllerInterface
{
}
```

If you want change view default, you must add the view your to the `Product` controller class.

```php
protected function view()
{
    return 'view-custom';
}
```

## Routes

The api endpoint should have these format:
| Verb   | URI                                            |
| ------ | ---------------------------------------------- |
| GET    | /api/{namespace}/admin/products             |
| GET    | /api/{namespace}/admin/products/{id}        |
| POST   | /api/{namespace}/admin/products             |
| PUT    | /api/{namespace}/admin/products/{id}        |
| DELETE | /api/{namespace}/admin/products/{id}        |
| PUT    | /api/{namespace}/admin/products/status/bulk |
| PUT    | /api/{namespace}/admin/products/status/{id} |
| ----   | ----                                           |
| GET    | /api/{namespace}/                   |
| GET    | /api/{namespace}/{id}               |
| POST   | /api/{namespace}/                   |
| PUT    | /api/{namespace}/{id}               |
| DELETE | /api/{namespace}/{id}               |
| PUT    | /api/{namespace}/status/bulk        |
| PUT    | /api/{namespace}/status/{id}       |
