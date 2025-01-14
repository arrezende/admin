<?php

namespace Source\Admin;

use Source\Session\Login;
use League\Plates\Engine;
use Source\Models\CategoryModel;
use Source\Models\GalleryModel;
use Source\Models\ProductModel;
use Source\Models\TagModel;

#[\AllowDynamicProperties]
class Dashboard {
    
    private $view;
    
    public function __construct()
    {
        // $this->view = new League\Plates\Engine(__DIR__."../../theme");
        $this->view = new Engine(__DIR__."/../../theme");
        Login::requireLogin();
    }
    
    public function home(): void
    {
        
        //Obriga o usuario a estar logado
        Login::requireLogin();
        echo $this->view->render("dash",[
            'products' => (new ProductModel)->find()->count(),
            'categories' => (new CategoryModel)->find()->count(),
            'tags' => (new TagModel)->find()->count(),
            'gallery' => (new GalleryModel)->find()->count()
        ]);
    }
    
    public function product(): void
    {
        Login::requireLogin();
        echo $this->view->render("product");
    }
    
    public function login(): void
    {
        Login::requireLogin();
        echo $this->view->render("login");
    }

    public function error(array $data): void
    {
        Login::requireLogin();
        echo "Erro - {$data["errcode"]}";
    }
}