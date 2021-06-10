<?php

namespace VCComponent\Laravel\Product\Test;

use Cviebrock\EloquentSluggable\ServiceProvider;
use Dingo\Api\Http\Response\Format\Json;
use Dingo\Api\Provider\LaravelServiceProvider;
use Dingo\Api\Transformer\Adapter\Fractal;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use VCComponent\Laravel\Product\Entities\Product;
use VCComponent\Laravel\Product\Providers\ProductRouteProvider;
use VCComponent\Laravel\Product\Providers\ProductServiceProvider;
use VCComponent\Laravel\Product\Transformers\ProductTransformer;
use VCComponent\Laravel\Product\Test\Stubs\Models\Product as TestEntity;
use VCComponent\Laravel\User\Providers\UserComponentProvider;
use VCComponent\Laravel\Category\Providers\CategoryServiceProvider;

use VCComponent\Laravel\Category\Entities\Category;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return HaiCS\Laravel\Generator\Providers\GeneratorServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [
            ProductServiceProvider::class,
            ProductRouteProvider::class,
            UserComponentProvider::class,
            LaravelServiceProvider::class,
            ServiceProvider::class,
            CategoryServiceProvider::class,
        ];
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/../src/database/factories');
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:TEQ1o2POo+3dUuWXamjwGSBx/fsso+viCCg9iFaXNUA=');
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('product.namespace', 'product-management');
        $app['config']->set('product.models', [
            'product' => TestEntity::class,
        ]);
        $app['config']->set('product.transformers', [
            'product' => ProductTransformer::class,
        ]);

        $app['config']->set('product.auth_middleware', [
            'admin'    => [],
            'frontend' => [],
        ]);
        $app['config']->set('product.test_mode', true);
        $app['config']->set('api', [
            'standardsTree'      => 'x',
            'subtype'            => '',
            'version'            => 'v1',
            'prefix'             => 'api',
            'domain'             => null,
            'name'               => null,
            'conditionalRequest' => true,
            'strict'             => false,
            'debug'              => true,
            'errorFormat'        => [
                'message'     => ':message',
                'errors'      => ':errors',
                'code'        => ':code',
                'status_code' => ':status_code',
                'debug'       => ':debug',
            ],
            'middleware'         => [
            ],
            'auth'               => [
            ],
            'throttling'         => [
            ],
            'transformer'        => Fractal::class,
            'defaultFormat'      => 'json',
            'formats'            => [
                'json' => Json::class,
            ],
            'formatsOptions'     => [
                'json' => [
                    'pretty_print' => false,
                    'indent_style' => 'space',
                    'indent_size'  => 2,
                ],
            ],
        ]);
    }
}
