<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;


class ProductModel extends DataLayer{

    public function __construct()
    {
        parent::__construct("products", ["name"]);
        
        //url, pdf, cover, content, category_id, status, created_at e updated_at
    }

    public function getProductsByCatId(int $id)
    {
        $likeSearch = "category_id LIKE '%,$id,%' OR category_id LIKE '$id,%' OR category_id LIKE '%,$id' OR category_id = '$id'";
        $products = $this->find("category_id LIKE $likeSearch")->fetch(true);
        foreach ($products as $prod) {
            $prod->url = !empty($prod->url) ? $prod->url : "produto/".makeUrl($prod->name)."/$prod->id";
        }
        return $products;
    }
    public function getProductById(int $id)
    {
        return $this->findById($id);
    }

    public function getAddImgByProdId($id)
    {
        $addImgs = (new ProductModel)->find("id = $id", null, 'additional_cover')->fetch(false);
        return $addImgs->additional_cover;
    }

}