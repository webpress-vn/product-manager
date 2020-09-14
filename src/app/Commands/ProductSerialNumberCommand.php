<?php

namespace VCComponent\Laravel\Generator\Commands;

use Illuminate\Console\Command;
use VCComponent\Laravel\Product\Repositories\ProductRepository;

class ProductSerialNumberCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:product-serial-number';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Package command description';

    protected $productRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ProductRepository $productRepository)
    {
        parent::__construct();
        $this->productEntity = $productRepository->getEntity();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $products =  $this->productEntity->orderBy('created_at', 'asc')->get()->groupBy('product_type');

        foreach ($products as $product) {
            $i = 0;
            foreach ($product as $item) {
                $i = $i + 1;
                $item['product_type_serial_number'] = $i;
                $item->save();
            }
        }
    }
}
