<?php

namespace VCComponent\Laravel\Product\Exports;

use App\Entities\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExports implements FromCollection, WithMapping, WithHeadings
{
    use Exportable;

    public function __construct(Collection $products)
    {
        $this->products = $products;

    }

    public function collection()
    {
        return $this->products;
    }

    public function map($products): array
    {
        return [
            $products->id,
            $products->name,
            $products->quantity,
            $products->status,
            $products->description,
            $products->price,
            $products->published_date,
            $products->sku,
            $products->created_at,

        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'quantity',
            'status',
            'description',
            'price',
            'published_date',
            'sku',
            'created_at',
        ];

    }

}
