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
use VCComponent\Laravel\Product\Transformers\VariantTransformer;
use VCComponent\Laravel\Tag\Transformers\TagTransformer;
use VCComponent\Laravel\Product\Transformers\ProductMetaTransformer;
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
        'variants',
        'productMetas'
    ];

    public function __construct($includes = [])
    {
        $this->setDefaultIncludes($includes);
    }

    public function transform($model)
    {
        $transform = [
            'id'             => (int) $model->id,
            'name'           => $model->name,
            'code'           => $model->code,
            'description'    => $model->description,
            'slug'           => $model->slug,
            'status'         => (int) $model->status,
            'product_type'   => $model->product_type,
            'price'          => (int) $model->price,
            'original_price' => (int) $model->original_price,
            'unit_price'     => $model->unit_price,
            'thumbnail'      => $model->thumbnail,
            'quantity'       => $model->quantity,
            'sold_quantity'  => $model->sold_quantity,
            'is_hot'         => $model->is_hot,
            'author_id'      => $model->author_id,
            'published_date' => $model->published_date,
            'sku'            => $model->sku,
            'order'          => $model->order,
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

    public function includeProductMetas($model)
    {
        return $this->collection($model->productMetas, new ProductMetaTransformer());
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

    public function includeVariants($model)
    {
        return $this->collection($model->variants, new VariantTransformer());
    }
}
