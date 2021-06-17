<?php

namespace VCComponent\Laravel\Product\Contracts;

interface ProductUtilitiesInterface
{
    public function getID();
    public function getName();
    public function getSlug();
    public function getQuantity();
    public function getSoldQuantity();
    public function getCode();
    public function getThumbnail();
    public function getOrder();
    public function getStatus();
    public function getDescription();
    public function getPrice();
    public function getOriginalPrice();
    public function getUnitPrice();
    public function getIsHot();
    public function getAuthorId();
    public function getPublishedDate();
    public function getSku();
    public function getCreatedAt();
    public function getUpdatedAt();
    public function getProductType();

    public function getFields($fields);

}
