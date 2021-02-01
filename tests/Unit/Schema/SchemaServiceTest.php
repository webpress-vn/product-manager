<?php

namespace VCComponent\Laravel\Product\Test\Unit\Schema;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Product\Entities\ProductSchema;
use VCComponent\Laravel\Product\Services\SchemaService;
use VCComponent\Laravel\Product\Test\TestCase;

class SchemaServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_schema_from_database()
    {
        factory(ProductSchema::class)->create();

        $service = new SchemaService();
        $data    = $service->get('products');

        $this->assertEquals($data[0]['name'], 'phone');
        $this->assertEquals($data[0]['label'], 'Phone');
        $this->assertEquals($data[0]['schema_type_id'], 1);
        $this->assertEquals($data[0]['schema_rule_id'], 5);
    }

    /** @test */
    public function can_get_schema_key_from_database()
    {
        factory(ProductSchema::class)->create();

        $service = new SchemaService();
        $data    = $service->getKey('products');

        $this->assertEquals($data[0], 'phone');
    }
}
