<?php
namespace VCComponent\Laravel\Product\Transformers;

use App\Transformers\SeoMetaTransformer;
use League\Fractal\TransformerAbstract;
use VCComponent\Laravel\Category\Transformers\CategoryTransformer;
use VCComponent\Laravel\Comment\Transformers\CommentCountTransformer;
use VCComponent\Laravel\Comment\Transformers\CommentTransformer;
use VCComponent\Laravel\MediaManager\Transformers\MediaTransformer;
use VCComponent\Laravel\Product\Entities\Attribute;
use VCComponent\Laravel\Product\Entities\AttributeValue;
use VCComponent\Laravel\Product\Transformers\ProductAttributeTransformer;
use VCComponent\Laravel\Tag\Transformers\TagTransformer;

class ProductTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'categories',
        'media',
        'comments',
        'commentCount',
        'seoMeta',
        'tags',
        'attributesValue',
        // 'productAttributes',
    ];
    public function __construct($includes = [])
    {
        $this->setDefaultIncludes($includes);
    }

    public function transform($model)
    {
        $author_name = $this->getNameAuthor($model);
        $transform   = [
            'id'             => (int) $model->id,
            'name'           => $model->name,
            'code'           => $model->code,
            'description'    => $model->description,
            'slug'           => $model->slug,
            'status'         => (int) $model->status,
            'price'          => (int) $model->price,
            'original_price' => (int) $model->original_price,
            'thumbnail'      => $model->thumbnail,
            'quantity'       => $model->quantity,
            'sold_quantity'  => $model->sold_quantity,
            'is_hot'         => $model->is_hot,
            'author'         => $author_name,
            'published_date' => $model->published_date,
            'sku'            => $model->sku,
        ];

        if ($model->productMetas->count()) {
            foreach ($model->productMetas as $item) {
                $transform[$item['key']] = $item['value'];
            }
        }
        $transform['timestamps'] = [
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
        return $transform;
    }

    public function includeCategories($model)
    {
        return $this->collection($model->categories, new CategoryTransformer());
    }

    public function includeMedia($model)
    {
        return $this->collection($model->media, new MediaTransformer());
    }

    public function includeComments($model)
    {
        return $this->collection($model->comments, new CommentTransformer());
    }

    public function includeTags($model)
    {
        if ($model->tags) {
            return $this->collection($model->tags, new TagTransformer());
        }
    }

    public function includeCommentCount($model)
    {
        return $this->collection($model->commentCount, new CommentCountTransformer());
    }

    public function includeSeoMeta($model)
    {
        if ($model->seoMeta) {
            return $this->item($model->seoMeta, new SeoMetaTransformer());
        }
    }

    public function includeAttributesValue($model)
    {
        return $this->collection($model->attributesValue, new ProductAttributeTransformer());
    }

    protected function getNameAuthor($model)
    {
        $author = $model->user;
        $name   = null;
        if ($author != null) {
            if ($author->first_name != null && $author->last_name != null) {
                $name = $author->first_name . ' ' . $author->last_name;
            } else if ($author->first_name != null || $author->last_name != null) {
                $name = $author->first_name ? $author->first_name : $author->last_name;
            } else {
                $name = $author->username;
            }
        }
        return $name;
    }

    // public function includeProductAttributes($model)
    // {
    //     $value_ids          = collect($model->attributesValue)->pluck('value_id');
    //     $attributes_value   = AttributeValue::select('id', 'attribute_id', 'label', 'value')->whereIn('id', $value_ids)->get();
    //     $attributes         = Attribute::select('name', 'id')->whereIn('id', $attributes_value->pluck('attribute_id')->unique())->get();
    //     $attributes_product = $attributes_value->map(function ($value, $key) use ($attributes) {
    //         $found = $attributes->search(function ($i) use ($value) {
    //             return $i->id === $value->attribute_id;
    //         });
    //         if ($found !== false) {
    //             return [$attributes->get($found)->name => $value];
    //         } else {
    //             return $value;
    //         }
    //     });
    //     dd($attributes_product);
    //     $result = $attributes_product->map(function ($value, $key) {
    //         $item_key   = collect($value)->keys()->first();
    //         $item_value = collect($value)->map(function ($vl, $ky) {
    //             return ['id' => $vl['id'], 'label' => $vl['label'], 'value' => $vl['value']];
    //         })->first();
    //         return array_merge($item_value, ['attribute' => $item_key]);
    //     })->groupBy( 'attribute');

    //     // return response()->json(['data' => $result]);
    //     return $this->item($result);
    // }
}
