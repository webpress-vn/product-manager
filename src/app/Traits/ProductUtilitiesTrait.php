<?php

namespace VCComponent\Laravel\Product\Traits;

trait ProductUtilitiesTrait
{
    public function getID() {
        return $this->id;
    }
    public function getName() {
        return $this->name;
    }
    public function getSlug() {
        return $this->slug;
    }
    public function getQuantity() {
        return $this->quantity;
    }
    public function getSoldQuantity() {
        return $this->sold_quantity;
    }
    public function getCode() {
        return $this->code;
    }
    public function getThumbnail() {
        return $this->thumbnail;
    }
    public function getOrder() {
        return $this->order;
    }
    public function getStatus() {
        return $this->status;
    }
    public function getDescription() {
        return $this->description;
    }
    public function getPrice() {
        return $this->price;
    }
    public function getOriginalPrice() {
        return $this->original_price;
    }
    public function getUnitPrice() {
        return $this->unit_price;
    }
    public function getIsHot() {
        return $this->is_hot;
    }
    public function getAuthorId() {
        return $this->author_id;
    }
    public function getPublishedDate() {
        return $this->published_date;
    }
    public function getSku() {
        return $this->sku;
    }
    public function getCreatedAt() {
        return $this->created_at;
    }
    public function getUpdatedAt() {
        return $this->updated_at;
    }
    public function getProductType() {
        return $this->product_type;
    }


    // get any field by product
    public function getFields($field)
    {
        if($this->getAttribute($field))
            return $this->getAttribute($field);
        else {
            return $this->getMetaField($field);
        }
    }

}
