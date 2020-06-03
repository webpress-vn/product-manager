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
