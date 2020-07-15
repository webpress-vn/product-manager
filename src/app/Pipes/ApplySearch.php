<?php

namespace VCComponent\Laravel\Product\Pipes;

use Closure;
use Illuminate\Http\Request;
use VCComponent\Laravel\Product\Contracts\Pipe;

class ApplySearch implements Pipe
{
    protected $request;
    protected $fields = [
        'name',
        'price',
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle($content, Closure $next)
    {
        if ($this->request->has('search')) {
            $search = $this->request->get('search');
            $fields = $this->fields;
            if (count($fields)) {
                $content = $content->where(function ($q) use ($fields, $search) {
                    foreach ($fields as $key => $field) {
                        $q = $q->orWhere($field, 'like', "%{$search}%");
                    }
                });
            }
        }
        return $next($content);
    }
}
