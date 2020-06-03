<?php

namespace VCComponent\Laravel\Product\Contracts;

use Closure;

interface Pipe
{
    public function handle($content, Closure $next);
}
