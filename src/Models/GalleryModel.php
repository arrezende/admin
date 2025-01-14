<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;


class GalleryModel extends DataLayer{

    public function __construct()
    {
        parent::__construct("gallery", ["name"]);

        //id, created_at e updated_at
    }

    public function findByCategory($id)
    {
        $likeSearch = "category_id LIKE '%,$id,%' OR category_id LIKE '$id,%' OR category_id LIKE '%,$id' OR category_id = '$id'";
        $items = (new GalleryModel)->find("category_id LIKE $likeSearch")->fetch(true);
        
        return $items;
    }
    public function findByProduct($id)
    {
        $items = (new GalleryModel)->find("product_id=:id", "id=$id")->fetch(true);
        
        return $items;
    }

}