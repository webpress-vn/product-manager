<?php

namespace VCComponent\Laravel\Product\Events;

use Illuminate\Queue\SerializesModels;

class ProductDeletedEvent
{
    use SerializesModels;


    public function __construct()
    {

    }
}
