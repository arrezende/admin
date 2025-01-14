<?php

namespace Source;

use CoffeeCode\Paginator\Paginator;
use Source\Admin\Gallery;
use Source\Models\CategoryModel;
use Source\Models\ProductModel;
use Source\Models\TagModel;
use Source\Models\GalleryModel;

#[\AllowDynamicProperties]
class Info
{
    public function getAllCategories()
    {
        // Pega todas as categorias e subcategorias
        $categories =  (new CategoryModel)->find()->order("priority ASC")->fetch(true);
        if (!empty($categories)) {
            foreach ($categories as $cat) {
                // if (!empty($cat->url) &&  substr($cat->url, -1) !== "/") {
                //     $cat->url = $cat->url . "/";
                // }
                $cat->url = !empty($cat->url) ? $cat->url : "categoria/" . makeUrl($cat->name) . "/$cat->id/$cat->father_id";
                if (strpos($cat->url, "/") == 0) {
                    $cat->url = ltrim($cat->url, '/');
                };
                // $cat->url = "categoria/" . makeUrl($cat->name) . "/$cat->id/$cat->father_id";
            }
        }
        return $categories;
    }

    public function getAllMainCategories()
    {
        // Pega apenas as categorias
        $categories =  (new CategoryModel)->find("father_id = :father_id", "father_id=0")->order('priority ASC')->fetch(true);
        if (!empty($categories)) {
            foreach ($categories as $cat) {
                // if (!empty($cat->url) &&  substr($cat->url, -1) !== "/") {
                //     $cat->url = $cat->url . "/";
                // }
                $cat->url = !empty($cat->url) ? $cat->url : "categoria/" . makeUrl($cat->name) . "/$cat->id/$cat->father_id";
                if (strpos($cat->url, "/") == 0) {
                    $cat->url = ltrim($cat->url, '/');
                };
                // $cat->url = "categoria/" . makeUrl($cat->name) . "/$cat->id/$cat->father_id";
            }
        }
        return $categories;
    }

    public function getAllTreeCategories()
    {
        // pega todas as categorias e subcategorias e estrutura em arvore, ou seja, se a categoria tem subs retorna as subs apenas se não tiver vem as categorias.
        $categories =  $this->getAllMainCategories();
        foreach ($categories as $keyCat => $cat) {
            $subCategory = $this->getAllCategoriesByFatherId(intval($cat->id));
            $subCategoryItems = !empty($subCategory) ? count($subCategory) : 0;

            if ($subCategoryItems == 0) {
                // if (!empty($cat->url) &&  substr($cat->url, -1) !== "/") {
                //     $cat->url = $cat->url . "/";
                // }
                $cat->url = !empty($cat->url) ? $cat->url : "categoria/" . makeUrl($cat->name) . "/";
                if (strpos($cat->url, "/") == 0) {
                    $cat->url = ltrim($cat->url, '/');
                };
                // $cat->url =  "categoria/" . makeUrl($cat->name) . "/$cat->id/$cat->father_id";
            }

            if ($subCategoryItems > 0) {
                $subCatAdicionais = [];
                for ($i = 0; $i < $subCategoryItems; $i++) {
                    $subcategoria = new CategoryModel();
                    $subcategoria->id = $subCategory[$i]->id;
                    $subcategoria->father_id = $subCategory[$i]->father_id;
                    $subcategoria->name = $subCategory[$i]->name;
                    $subcategoria->description = $subCategory[$i]->description;
                    $subcategoria->cover = $subCategory[$i]->cover;
                    $subcategoria->pdf = $subCategory[$i]->pdf;
                    $subcategoria->gallery_id = $subCategory[$i]->gallery_id;
                    // $subcategoria->url = !empty($subCategory[$i]->url) ? $subCategory[$i]->url : "categoria/" . makeUrl($subCategory[$i]->name) . "/{$subCategory[$i]->id}/{$subCategory[$i]->father_id}";
                    $subcategoria->url = !empty($subCategory[$i]->url) ? $subCategory[$i]->url : "categoria/" . makeUrl($subCategory[$i]->name) . "/";
                    if (strpos($subcategoria->url, "/") == 0) {
                        $subcategoria->url = ltrim($subcategoria->url, '/');
                    };
                    $subCatAdicionais[] = $subcategoria;
                }

                unset($categories[$keyCat]);
                array_push($categories, ...$subCatAdicionais);
            }
        }
        return array_values($categories);
        // return $categories;
    }

    // public function getCategoryByFatherId(int $father_id)
    // {
    //     return (new CategoryModel)->find("father_id = :father_id", "father_id=$father_id")->fetch(true);
    // }

    public function getAllCategoriesByFatherId(int $father_id)
    {

        $categories =  (new CategoryModel)->find("father_id = :father_id", "father_id=$father_id")->order('priority ASC')->fetch(true);
        if (!empty($categories)) {
            foreach ($categories as $cat) {
                // if (!empty($cat->url) && substr($cat->url, -1) !== "/") {
                //     $cat->url = $cat->url . "/";
                // }
                $cat->url = !empty($cat->url) ? $cat->url : "categoria/" . makeUrl($cat->name) . "/$cat->id/$cat->father_id";
                if (strpos($cat->url, "/") == 0) {
                    $cat->url = ltrim($cat->url, '/');
                };
                // $cat->url = "categoria/" . makeUrl($cat->name) . "/$cat->id/$cat->father_id";
            }
        }
        return $categories;
    }

    public function getAllCategoriesIdByFatherId(int $father_id)
    {

        $categories =  (new CategoryModel)->find("father_id = :father_id", "father_id=$father_id", "id")->fetch(true);

        return $categories;
    }


    public function getCategoryById(int $id)
    {
        return (new CategoryModel())->findById($id);
    }

    public function getTitleCategoryById(int $id)
    {
        return (new CategoryModel())->find("id = :id", "id=$id")->fetch();
    }

    public function getProductById(int $id)
    {
        return (new ProductModel())->findById($id);
    }



    public function getProductsByCatId($id)
    {
        $likeSearch = "category_id LIKE '%,$id,%' OR category_id LIKE '$id,%' OR category_id LIKE '%,$id' OR category_id = '$id'";
        $products = (new ProductModel())->find($likeSearch)->fetch(true);
        if (!empty($products)) {
            foreach ($products as $prod) {
                // if (!empty($prod->url) && substr($prod->url, -1) !== "/") {
                //     $prod->url = $prod->url . "/";
                // }
                $prod->url = !empty($prod->url) ? $prod->url : "produto/" . makeUrl($prod->name) . "/";
                if (strpos($prod->url, "/") == 0) {
                    $prod->url = ltrim($prod->url, '/');
                };
                // $prod->url = "produto/" . makeUrl($prod->name) . "/$prod->id" . "/$prod->category_id";
            }
        }
        return $products;
    }

    public function getRelatedProductsByCatIdLimited($catId, $prodId, $quantity)
    {
        $likeSearch = "category_id LIKE '%,$catId,%' OR category_id LIKE '$catId,%' OR category_id LIKE '%,$catId' OR category_id = '$catId'";
        $products = (new ProductModel())->find($likeSearch)->limit($quantity)->fetch(true);

        if (!empty($products)) {
            foreach ($products as $key => $prod) {
                if ($prod->id == $prodId) {
                    $remover = $key;
                }
                // if (!empty($prod->url) && substr($prod->url, -1) !== "/") {
                //     $prod->url = $prod->url . "/";
                // }
                $prod->url = !empty($prod->url) ? $prod->url : "produto/" . makeUrl($prod->name) . "/";
                if (strpos($prod->url, "/") == 0) {
                    $prod->url = ltrim($prod->url, '/');
                };
                // $prod->url = "produto/" . makeUrl($prod->name) . "/$prod->id" . "/$prod->category_id";
            }
            array_splice($products, $remover, 1);
        }
        return $products;
    }

    public function getRelatedProductsByCatId($catId, $prodId)
    {
        $likeSearch = "category_id LIKE '%,$catId,%' OR category_id LIKE '$catId,%' OR category_id LIKE '%,$catId' OR category_id = '$catId'";
        $products = (new ProductModel)->find($likeSearch)->fetch(true);

        if (!empty($products)) {
            foreach ($products as $key => $prod) {
                if ($prod->id == $prodId) {
                    $remover = $key;
                }
                // if (!empty($prod->url) && substr($prod->url, -1) !== "/") {
                //     $prod->url = $prod->url . "/";
                // }
                $prod->url = !empty($prod->url) ? $prod->url : "produto/" . makeUrl($prod->name) . "/";
                if (strpos($prod->url, "/") == 0) {
                    $prod->url = ltrim($prod->url, '/');
                };
                // $prod->url = "produto/" . makeUrl($prod->name) . "/$prod->id" . "/$prod->category_id";
            }
            array_splice($products, $remover, 1);
        }
        return $products;
    }

    public function getAllProducts()
    {
        $products =  (new ProductModel)->find()->fetch(true);
        foreach ($products as $prod) {
            // if (!empty($prod->url) && substr($prod->url, -1) !== "/") {
            //     $prod->url = $prod->url . "/";
            // }
            $prod->url = !empty($prod->url) ? $prod->url : "produto/" . makeUrl($prod->name) . "/";
            if (strpos($prod->url, "/") == 0) {
                $prod->url = ltrim($prod->url, '/');
            };
            // $prod->url = "produto/" . makeUrl($prod->name) . "/$prod->id" . "/$prod->category_id";
        }
        return $products;
    }

    public function getAllProductsLimited($quantity)
    {
        $products =  (new ProductModel)->find()->limit($quantity)->fetch(true);
        foreach ($products as $prod) {
            // if (!empty($prod->url) && substr($prod->url, -1) !== "/") {
            //     $prod->url = $prod->url . "/";
            // }
            $prod->url = !empty($prod->url) ? $prod->url : "produto/" . makeUrl($prod->name) . "/";
            if (strpos($prod->url, "/") == 0) {
                $prod->url = ltrim($prod->url, '/');
            };
            // $prod->url = "produto/" . makeUrl($prod->name) . "/$prod->id" . "/$prod->category_id";
        }
        return $products;
    }
    public function getAllProductsByCatIdLimited($id, $quantity)
    {
        // $products =  (new ProductModel)->find($likeSearch)->limit($quantity)->fetch(true);
        $likeSearch = "category_id LIKE '%,$id,%' OR category_id LIKE '$id,%' OR category_id LIKE '%,$id' OR category_id = '$id'";

        $products = (new ProductModel())->find($likeSearch)->fetch();
        foreach ($products as $prod) {
            // if (!empty($prod->url) && substr($prod->url, -1) !== "/") {
            //     $prod->url = $prod->url . "/";
            // }
            $prod->url = !empty($prod->url) ? $prod->url : "produto/" . makeUrl($prod->name) . "/";
            if (strpos($prod->url, "/") == 0) {
                $prod->url = ltrim($prod->url, '/');
            };
            // $prod->url = "produto/" . makeUrl($prod->name) . "/$prod->id" . "/$prod->category_id";
        }
        return $products;
    }

    public function getGalleryImgByCatId($catId)
    {

        $items = (new GalleryModel)->find("category_id=:id", "id=$catId")->fetch(true);

        return $items;
    }

    public function getGalleryImgByProdId($prodId)
    {

        $items = (new GalleryModel)->find("product_id=:id", "id=$prodId")->fetch(true);

        return $items;
    }

    public function productsPagination($id, $quantity)
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $urlBase = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $getPage = !empty(explode('page/', $urlBase)[1]) ? intval(explode('page/', $urlBase)[1]) : null;
        $urlBase = explode('page/', $urlBase)[0];

        $likeSearch = "category_id LIKE '%,$id,%' OR category_id LIKE '$id,%' OR category_id LIKE '%,$id' OR category_id = '$id'";
        $row = (new ProductModel())->find($likeSearch)->count();

        // $page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
        // preg_match('/page=(\d+)/', $getPage, $matches);
        // $page = isset($matches[1]) ? (int)$matches[1] : null;
        $page = $getPage;

        if (substr($urlBase, -1) != '/') {
            $urlBase .= '/';
        }

        // var_dump($page);
        // die;
        $page = !is_null($page) ? $page : 1;
        $pager = new Paginator($urlBase . "page/");
        $pager->pager($row, $quantity, $page);
        return $pager;
    }

    public function productsByCatIdPaginated($id, $pager)
    {
        $likeSearch = "category_id LIKE '%,$id,%' OR category_id LIKE '$id,%' OR category_id LIKE '%,$id' OR category_id = '$id'";
        $products = (new ProductModel())->find($likeSearch)->limit($pager->limit())->offset($pager->offset())->fetch(true);
        if (!empty($products)) {
            foreach ($products as $prod) {

                $prod->url = !empty($prod->url) ? $prod->url : "produto/" . makeUrl($prod->name) . "/";
                if (strpos($prod->url, "/") == 0) {
                    $prod->url = ltrim($prod->url, '/');
                };
                // $prod->url = "produto/" . makeUrl($prod->name) . "/$prod->id" . "/$prod->category_id";
            }
        }
        return $products;
    }

    public function getAllProductsByAllSubatsPagination($catId, $quantity)
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $urlBase = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $getPage = !empty(explode('page/', $urlBase)[1]) ? intval(explode('page/', $urlBase)[1]) : null;
        $urlBase = explode('page/', $urlBase)[0];

        $likeSearch = "";

        for ($i = 0; $i < count($catId); $i++) {
            if ($i == 0) {
                $likeSearch = "category_id LIKE '%,{$catId[$i]->id},%' OR category_id LIKE '{$catId[$i]->id},%' OR category_id LIKE '%,{$catId[$i]->id}' OR category_id = '{$catId[$i]->id}'";
            } else {
                $likeSearch .= " OR category_id LIKE '%,{$catId[$i]->id},%' OR category_id LIKE '{$catId[$i]->id},%' OR category_id LIKE '%,{$catId[$i]->id}' OR category_id = '{$catId[$i]->id}'";
            }
        }

        $row = (new ProductModel())->find($likeSearch, null, "id")->count();


        $page = $getPage;

        if (substr($urlBase, -1) != '/') {
            $urlBase .= '/';
        }

        $page = !is_null($page) ? $page : 1;
        $pager = new Paginator($urlBase . "page/");
        $pager->pager($row, $quantity, $page);
        return $pager;
    }

    public function getAllProductsByAllSubcatsPaginated($catId, $pager)
    {
        $likeSearch = "";

        for ($i = 0; $i < count($catId); $i++) {
            if ($i == 0) {
                $likeSearch = "category_id LIKE '%,{$catId[$i]->id},%' OR category_id LIKE '{$catId[$i]->id},%' OR category_id LIKE '%,{$catId[$i]->id}' OR category_id = '{$catId[$i]->id}'";
            } else {
                $likeSearch .= " OR category_id LIKE '%,{$catId[$i]->id},%' OR category_id LIKE '{$catId[$i]->id},%' OR category_id LIKE '%,{$catId[$i]->id}' OR category_id = '{$catId[$i]->id}'";
            }
        }

        $products = (new ProductModel())->find($likeSearch, null, "name,url,cover")->limit($pager->limit())->offset($pager->offset())->fetch(true);
        if (!empty($products)) {
            foreach ($products as $prod) {
                if (!empty($prod->url) && substr($prod->url, -1) !== "/") {
                    $prod->url = $prod->url . "/";
                }
                $prod->url = !empty($prod->url) ? $prod->url : "produto/" . makeUrl($prod->name) . "/";
                if (strpos($prod->url, "/") == 0) {
                    $prod->url = ltrim($prod->url, '/');
                };
                // $prod->url = "produto/" . makeUrl($prod->name) . "/$prod->id" . "/$prod->segment_id";
            }
        }

        return $products;
    }

    public function productsTagPagination($id, $quantity)
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $urlBase = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $getPage = !empty(explode('page/', $urlBase)[1]) ? intval(explode('page/', $urlBase)[1]) : null;
        $urlBase = explode('page/', $urlBase)[0];

        $likeSearch = "tags LIKE '%,$id,%' OR tags LIKE '$id,%' OR tags LIKE '%,$id' OR tags = '$id'";
        $row = (new ProductModel())->find($likeSearch)->count();

        // $page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
        // preg_match('/page=(\d+)/', $getPage, $matches);
        // $page = isset($matches[1]) ? (int)$matches[1] : null;
        $page = $getPage;

        if (substr($urlBase, -1) != '/') {
            $urlBase .= '/';
        }

        // var_dump($page);
        // die;
        $page = !is_null($page) ? $page : 1;
        $pager = new Paginator($urlBase . "page/");
        $pager->pager($row, $quantity, $page);
        return $pager;
    }

    public function productsByTagIdPaginated($id, $pager)
    {
        $likeSearch = "tags LIKE '%,$id,%' OR tags LIKE '$id,%' OR tags LIKE '%,$id' OR tags = '$id'";
        $products = (new ProductModel())->find($likeSearch)->limit($pager->limit())->offset($pager->offset())->fetch(true);
        if (!empty($products)) {
            foreach ($products as $prod) {

                $prod->url = !empty($prod->url) ? $prod->url : "produto/" . makeUrl($prod->name) . "/";
                if (strpos($prod->url, "/") == 0) {
                    $prod->url = ltrim($prod->url, '/');
                };
                // $prod->url = "produto/" . makeUrl($prod->name) . "/$prod->id" . "/$prod->category_id";
            }
        }
        return $products;
    }

    public function pagerRender($pager)
    {
        return $pager->render();
    }

    public function getAllProductsName()
    {
        return (new ProductModel())->find(null, null, "name")->fetch(true);
    }

    public function getProductsByName($name)
    {
        $products = (new ProductModel())->find("name LIKE '%$name%'")->fetch(true);

        if (!empty($products)) {
            foreach ($products as $prod) {
                $prod->url = !empty($prod->url) ? $prod->url : "produto/" . makeUrl($prod->name) . "/";
                if (strpos($prod->url, "/") == 0) {
                    $prod->url = ltrim($prod->url, '/');
                };
                // $prod->url = "produto/" . makeUrl($prod->name) . "/$prod->id" . "/$prod->category_id";
            }
        }
        return $products;
    }

    public function getImgsByProductId($id) {
        $imgs = (new GalleryModel())->find("product_id = $id")->fetch(true);
        return $imgs;
    }

    // ====== *** TAGS *** ======

    public function getAllTags()
    {
        $tags = (new TagModel)->find()->fetch(true);
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                // if (!empty($tag->url) &&  substr($tag->url, -1) !== "/") {
                //     $tag->url = $tag->url . "/";
                // }
                $tag->url = !empty($tag->url) ? $tag->url : "tag/" . makeUrl($tag->tag_name . '-' . $tag->id);
                if (strpos($tag->url, "/") == 0) {
                    $tag->url = ltrim($tag->url, '/');
                };
                // $tag->url = "categoria/" . makeUrl($tag->name) . "/$tag->id/$tag->father_id";
            }
        }
        return  $tags;
    }

    public function getTitleTagById(int $id) {
        $tagTitle = (new TagModel)->findById($id);

        return $tagTitle->tag_name;
    }
}
