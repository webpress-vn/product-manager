<?php

namespace VCComponent\Laravel\Product\Pipes;

use Closure;
use Illuminate\Http\Request;
use VCComponent\Laravel\Product\Contracts\Pipe;

class ApplyOrderBy implements Pipe
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle($content, Closure $next)
    {
        if ($this->request->has('order_by')) {
            $orderBy = (array) json_decode($this->request->get('order_by'));
            if (count($orderBy) > 0) {
                foreach ($orderBy as $key => $value) {
                    $content = $content->orderBy($key, $value);
                }
            }
        } else {
            $content = $content->orderBy('id', 'desc');
        }
        return $next($content);
    }
}
