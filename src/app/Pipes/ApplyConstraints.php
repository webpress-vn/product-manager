<?php

namespace VCComponent\Laravel\Product\Pipes;

use Closure;
use Illuminate\Http\Request;
use VCComponent\Laravel\Product\Contracts\Pipe;

class ApplyConstraints implements Pipe
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle($content, Closure $next)
    {
        if ($this->request->has('constraints')) {
            $constraints = (array) json_decode($this->request->get('constraints'));
            if (count($constraints)) {
                $content = $content->where($constraints);
            }
        }
        return $next($content);
    }
}
