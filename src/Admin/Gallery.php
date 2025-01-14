<?php

namespace Source\Admin;

use League\Plates\Engine;
use Source\Models\CategoryModel;
use CoffeeCode\Uploader\Image;
use Source\Models\GalleryModel;
use Source\Models\ProductModel;
use Source\Session\Login;

#[\AllowDynamicProperties]
class Gallery
{

    private $view;

    public function __construct()
    {
        $this->view = new Engine(__DIR__ . "/../../theme");
        Login::requireLogin();
    }

    public function home(): void
    {
        $products = $this->getProducts();
        $categories = $this->getCategories();
        echo $this->view->render("galleries", [
            "products" => $products,
            "categories" => $categories,
        ]);
    }

    public function categories(): void
    {
        $categories = $this->getCategories();
        echo $this->view->render("galleries", [
            "products" => [],
            "categories" => $categories,
        ]);
    }

    public function categoriesEdit(array $data): void
    {
        $category = $this->getCategoryById($data['id']);
        $gallery = $this->getGalleryByCategory($data['id']);
        echo $this->view->render("gallery", [
            "title" => "Categorias",
            "urlForm" => "galerias/categorias/atualizar/$data[id]",
            "urlDelete" => "galerias/remover",
            "category" => $category,
            "images" => $gallery
        ]);
    }

    public function categoriesUpdate(array $data)
    {
        $img = new Image(UPLOAD_FOLDER . 'category', 'gallery', false);
        $gallery = new GalleryModel;
        if ($_FILES['file']) {
            $gallery->category_id = $data['id'];
            $gallery->name = $img->upload($_FILES['file'], $_FILES['file']['name']);
            $gallery->save();
            echo json_encode("Cadastro Realizado com sucesso");
        }
    }

    public function products(): void
    {
        $products = $this->getProducts();
        echo $this->view->render("galleries", [
            "categories" => [],
            "products" => $products,
        ]);
    }

    public function productsEdit(array $data): void
    {
        $product = $this->getProductById($data['id']);
        $gallery = $this->getGalleryByProduct($data['id']);
        echo $this->view->render("gallery", [
            "title" => "Produtos",
            "urlForm" => "galerias/produtos/atualizar/$data[id]",
            "urlDelete" => "galerias/remover",
            "product" => $product,
            "images" => $gallery,
            "urlNewProduct" => url("produtos/adicionar")
        ]);
    }

    public function productsUpdate(array $data)
    {
        $img = new Image(UPLOAD_FOLDER . 'product', 'gallery', false);
        $gallery = new GalleryModel;

        if (!empty($_FILES['file'])) {
            $gallery->product_id = $data['id'];
            $gallery->name = $img->upload($_FILES['file'], $_FILES['file']['name']);
            $gallery->save();
            echo json_encode("Cadastro Realizado com sucesso");
        }
    }

    public function delete(array $data)
    {
        $gallery = $this->getGalleryById($data['id']);
        if ($gallery->destroy()) {
            unlink($gallery->name);
            return true;
        } else {
            return "Erro ao remover";
        }
    }

    private function getProducts()
    {
        return (new ProductModel())->find()->fetch(true);
    }

    private function getCategories()
    {
        return (new CategoryModel())->find()->fetch(true);
    }

    private function getCategoryById(int $id)
    {
        return (new CategoryModel())->findById($id)->data();
    }

    private function getGalleryByCategory(int $id)
    {
        return (new GalleryModel)->findByCategory($id);
    }

    private function getProductById(int $id)
    {
        return (new ProductModel())->findById($id)->data();
    }

    private function getGalleryByProduct(int $id)
    {
        return (new GalleryModel)->findByProduct($id);
    }

    private function getGalleryById(int $id)
    {
        return (new GalleryModel())->findById($id);
    }
}
