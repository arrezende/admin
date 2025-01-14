<?php

namespace Source\Admin;

use CoffeeCode\Uploader\File;
use CoffeeCode\Uploader\Image;
use CoffeeCode\Paginator\Paginator;
use League\Plates\Engine;
use Source\Models\CategoryModel;
use Source\Models\ProductModel;
use Source\Session\Login;
use stdClass;

#[\AllowDynamicProperties]
class Category
{

    private $view;
    private $role;

    public function __construct()
    {
        $this->view = new Engine(__DIR__ . "/../../theme");
        $this->role = 'admin';
        Login::requireLogin();
    }

    public function home(): void
    {
        $row = (new CategoryModel())->find()->count();
        $page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
        $page = !is_null($page) ? $page : 1;
        $pager = new Paginator(url("categorias?page="));
        $pager->pager($row, 5, $page);

        $categories = (new CategoryModel())->find()->limit($pager->limit())->offset($pager->offset())->order("priority ASC")->fetch(true);


        if ($categories) {
            foreach ($categories as $category) {
                // $productCount = (new ProductModel())->find("category_id = :category_id", "category_id=$category->id")->count();
                $likeSearch = "category_id LIKE '%,$category->id,%' OR category_id LIKE '$category->id,%' OR category_id LIKE '%,$category->id' OR category_id = '$category->id'";
                $productCount = (new ProductModel())->find($likeSearch)->count();
                $category->total_products = $productCount;
            }
        }
        echo $this->view->render("categories", [
            'categories' => $categories,
            'pagination' => $pager->render()
            // 'pagination' => $this->getPagination($categories)
        ]);
    }

    public function new(): void
    {
        $type = filter_input(INPUT_POST, "type", FILTER_SANITIZE_STRING);
        if ($type == "new") {
            $this->saveNewCategory();
        }
        // echo $this->view->render("category-new",[
        //     "categories" => $this->getAllCategories()
        // ]);
        $category = new stdClass();
        $category->name = "";
        $category->sub_name = "";
        $category->url = "";
        $category->description = "";
        $category->card_description = "";
        $category->cover = "";
        $category->father_id = "";
        $category->pdf = "";
        $category->priority = "";

        $allUrls = $this->getAllCatsUrlExistents();
        echo $this->view->render("category", [
            "category" => $category,
            "allUrls" => $allUrls,
            "formAction" => url("categorias/adicionar"),
            "title" => "Nova Categoria",
            "type" => "new",
            "categories" => $this->getAllCategories(),
            "treeSortCategories" => (new CategoryModel)->getAllTreeSortCategories(),
        ]);
    }

    public function edit(array $data): void
    {
        $type = filter_input(INPUT_POST, "type");
        if ($type == "edit") {
            $this->saveEditCategory($data);
        }
        $category = $this->getCategoryById($data['id']);
        $allUrls = $this->getAllCatsUrlExistents();
        echo $this->view->render("category", [
            "category" => $category,
            "allUrls" => $allUrls,
            "formAction" => url("categorias/editar/{$category->id}"),
            "title" => "Editar Categoria - $category->name",
            "type" => "edit",
            "categories" => $this->getAllCategories(),
            "treeSortCategories" => (new CategoryModel)->getAllTreeSortCategories(),
        ]);
    }

    public function deleteImage(array $data)
    {
        $category = $this->getCategoryById($data['id']);
        $category->cover = null;
        $category->save();
        return $category;
    }

    public function search(): void
    {
        $name = filter_var($_POST['s'], FILTER_SANITIZE_SPECIAL_CHARS);
        $categories = (new CategoryModel())->find("name LIKE '%$name%'")->fetch(true);


        if ($categories) {
            foreach ($categories as $category) {
                // $productCount = (new ProductModel())->find("category_id = :category_id", "category_id=$category->id")->count();
                $likeSearch = "category_id LIKE '%,$category->id,%' OR category_id LIKE '$category->id,%' OR category_id LIKE '%,$category->id' OR category_id = '$category->id'";
                $productCount = (new ProductModel())->find($likeSearch)->count();
                $category->total_products = $productCount;
            }
        }
        echo $this->view->render("categories", [
            'categories' => $categories,
            "pagination" => NULL
        ]);
    }
    public function delete(array $data)
    {
        $category = $this->getCategoryById($data['id']);

        return $category->destroy();
    }
    // public function delete(array $data)
    // {
    //     $category = $this->getCategoryById($data['id']);
    //     $category->status = "trash";
    //     return $category->save();
    // }

    public function activate(array $data)
    {
        $category = $this->getCategoryById($data['id']);
        $category->status = "post";
        return $category->save();
    }



    // private function getCategories2()
    // {
    //     $pager = $this->pagination();
    //     print_r($pager);
    //     // return (new CategoryModel())->find()->limit($pager->limit())->offset($pager->offset())->fetch(true);
    // }

    // private function getCategories()
    // {
    //     $row = (new CategoryModel())->find()->count();
    //     $page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);

    //     return (new CategoryModel())->find()->limit(4)->offset(0)->fetch(true);
    // }

    private function getPagination($categories)
    {
        $row = (!is_null($categories)) ? count($categories) : 0;
        $page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
        $page = !is_null($page) ? $page : 1;
        $pager = new Paginator(url("categorias?page="));
        $pager->pager($row, 5, $page);
        return $pager->render();
    }

    public function getAllCategories()
    {
        $categories =  (new CategoryModel)->find()->fetch(true);
        if (!empty($categories)) {
            foreach ($categories as $cat) {
                $cat->url = !empty($cat->url) ? $cat->url : "categoria/" . makeUrl($cat->name) . "/$cat->id";
            }
        }
        return $categories;
    }

    private function getCategoryById(int $id)
    {
        return (new CategoryModel())->findById($id);
    }

    private function saveNewCategory()
    {
        $pdf = new File(UPLOAD_FOLDER . 'category', 'files', false);
        $img = new Image(UPLOAD_FOLDER . 'category', 'images', false);

        $newCategory = new CategoryModel();
        $newCategory->name = $_POST['name'];
        $newCategory->sub_name = !empty($_POST['sub_name']) ? $_POST['sub_name'] : '';

        if (!empty($_POST['url'])) {
            $urlProcess = $_POST['url'];
            if (strpos($urlProcess, "/") == 0) {
                $urlProcess = ltrim($urlProcess, '/');
            };
            if (strrpos($urlProcess, "/")) {
                $urlProcess = rtrim($urlProcess, '/');
            };
            $urlProcess = preg_replace('/\s+/', '-', $urlProcess);
        } else {
            $urlProcess = "categoria/" . makeUrl($newCategory->name);
        }
        $newCategory->url = !empty($urlProcess) ? $urlProcess : null;

        $newCategory->description = $_POST['description'];
        $newCategory->card_description = !empty($_POST['card_description']) ? $_POST['card_description'] : '';
        $newCategory->father_id = intval($_POST['father_id']);

        if (!empty($_FILES['cover']['name'])) {
            $newCategory->cover =  $img->upload($_FILES['cover'], $_POST['name']);
        }
        if (!empty($_FILES['pdf']['name'])) {
            $newCategory->pdf =  $pdf->upload($_FILES['pdf'], $_POST['name']);
        }

        $newCategory->priority = !empty($_POST['priority']) ? intval($_POST['priority']) : 99;

        $category = $newCategory->save();

        if (!empty($newCategory->url)) {
            $this->geraHtaccess($newCategory);
        }

        if (!$category) {
            $response = [
                "success" => false,
                "message" => "Erro ao salvar a categoria"
            ];
        } else {
            $response = [
                "success" => true,
                "message" => "Categoria salva com sucesso",
                "clearInputs" => true
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    private function saveEditCategory(array $data)
    {
        $pdf = new File(UPLOAD_FOLDER . 'category', 'files', false);
        $img = new Image(UPLOAD_FOLDER . 'category', 'images', false);
        $editCategory = $this->getCategoryById($data['id']);
        $editCategory->name = $_POST['name'];
        $editCategory->sub_name = !empty($_POST['sub_name']) ? $_POST['sub_name'] : '';

        if (!empty($_POST['url'])) {
            $urlProcess = $_POST['url'];
            if (strpos($urlProcess, "/") == 0) {
                $urlProcess = ltrim($urlProcess, '/');
            };
            if (strrpos($urlProcess, "/")) {
                $urlProcess = rtrim($urlProcess, '/');
            };
            $urlProcess = preg_replace('/\s+/', '-', $urlProcess);
        } else {
            $urlProcess = "categoria/" . makeUrl($editCategory->name);
        }
        $editCategory->url = !empty($urlProcess) ? $urlProcess : null;
        
        $editCategory->description = $_POST['description'];
        $editCategory->card_description = !empty($_POST['card_description']) ? $_POST['card_description'] : '';
        if (!empty($_FILES['cover']['name'])) {
            $editCategory->cover =  $img->upload($_FILES['cover'], $_POST['name']);
        }
        if (!empty($_FILES['pdf']['name'])) {
            $editCategory->pdf =  $pdf->upload($_FILES['pdf'], $_POST['name']);
        }
        $editCategory->father_id = intval($_POST['father_id']);
        $editCategory->priority = !empty($_POST['priority']) ? intval($_POST['priority']) : 99;

        $category = $editCategory->save();

        if (!empty($editCategory->url)) {
            $this->geraHtaccess($editCategory);
        }

        if (!$category) {
            $response = [
                "success" => false,
                "message" => "Erro ao salvar a categoria"
            ];
        } else {
            $response = [
                "success" => true,
                "message" => "Categoria salva com sucesso",

            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    private function getAllCatsUrlExistents()
    {
        $allUrls = (new CategoryModel())->find(NULL, NULL, "url")->fetch(true);
        return $allUrls;
    }


    private function geraHtaccess($data)
    {
        $htaccessFilePath = __DIR__ . "/../../../.htaccess";

        // Ler o conteúdo atual do arquivo .htaccess
        $currentContent = file_get_contents($htaccessFilePath);
        // Código que você deseja inserir antes do trecho específico
        $newRule1 = "RewriteRule ^$data->url/?$ produtos.php?id=$data->id&father_id=$data->father_id [NC,L]\n";
        $newRule2 = "RewriteRule ^{$data->url}\/([a-z,0-9,_-]+)\/([0-9-]+)\/?$ produtos.php?id={$data->id}&father_id={$data->father_id} [NC,L]\n";

        // Localize o trecho específico onde deseja inserir o novo código
        $searchPattern = "RewriteRule ^([a-z,0-9,A-Z,_-]+)\/?$ $1.php";

        // Sobrescreve a linha do htacess se houver duplicidade
        $searchDuplicate1 = explode('?$', $newRule1)[0];
        $searchDuplicate2 = explode('?$', $newRule2)[0];
        
        if (strpos($currentContent, $searchDuplicate1) !== false) {
            if (preg_match("/" . preg_quote($searchDuplicate1, "/") . ".*$/m", $currentContent, $matches)) {
                $capturedDuplicate = $matches[0];
                if (trim($capturedDuplicate) != trim($newRule1)) {
                    $modifiedContent = preg_replace("/" . preg_quote($capturedDuplicate, "/") . "\n?/", $newRule1, $currentContent);
                    file_put_contents($htaccessFilePath, $modifiedContent);
                    $currentContent = file_get_contents($htaccessFilePath);
                    // return false;
                } else {
                    return false;
                }
            }
        }
        
        if (strpos($currentContent, $searchDuplicate2) !== false) {
            if (preg_match("/" . preg_quote($searchDuplicate2, "/") . ".*$/m", $currentContent, $matches)) {
                $capturedDuplicate = $matches[0];
                if (trim($capturedDuplicate) != trim($newRule2)) {
                    $modifiedContent = preg_replace("/" . preg_quote($capturedDuplicate, "/") . "\n?/", $newRule2, $currentContent);
                    file_put_contents($htaccessFilePath, $modifiedContent);
                    return false;
                } else {
                    return false;
                }
            }
        }

        // Inserir o novo código antes do trecho específico
        $modifiedContent = preg_replace("/" . preg_quote($searchPattern, "/") . "/", $newRule1 . $newRule2 . "$0", $currentContent);

        // Escrever o conteúdo modificado de volta no arquivo .htaccess
        file_put_contents($htaccessFilePath, $modifiedContent);
    }
}
