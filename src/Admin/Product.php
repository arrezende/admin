<?php

namespace Source\Admin;

use Exception;
use CoffeeCode\Uploader\File;
use CoffeeCode\Uploader\Image;
use CoffeeCode\Paginator\Paginator;
use League\Plates\Engine;
use Source\Models\CategoryModel;
use Source\Models\ProductModel;
use Source\Models\TagModel;
use Source\Session\Login;
use stdClass;

#[\AllowDynamicProperties]
class Product
{

    private $view;

    public function __construct()
    {
        $this->view = new Engine(__DIR__ . "/../../theme");
        Login::requireLogin();
    }

    public function home(): void
    {
        $row = (new ProductModel())->find()->count();
        $page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);

        $page = !is_null($page) ? $page : 1;
        $pager = new Paginator(url("produtos?page="));
        $pager->pager($row, 5, $page);

        $products = (new ProductModel())->find(NULL, NULL, "id,name,cover,category_id,priority,tags,created_at,status")->limit($pager->limit())->offset($pager->offset())->order("priority ASC")->fetch(true);
        if (!empty($products)) {
            foreach ($products as $prod) {
                if (count(explode(',', $prod->category_id)) > 1) {
                    $categoriesArray = explode(',', $prod->category_id);
                    $categoriesName = [];
                    foreach ($categoriesArray as $cat) {
                        $categoriesName[] = (new CategoryModel())->findById($cat)->name;
                    }
                    $categoryName = implode(", ", $categoriesName);
                } else {
                    $categoryName = is_int($prod->category_id) && !empty(((new CategoryModel())->findById($prod->category_id))->name) ? ((new CategoryModel())->findById($prod->category_id))->name : "Sem Categoria";
                }
                $prod->category_name = $categoryName;

                if (count(explode(',', $prod->tags)) > 1) {
                    $tagsArray = explode(',', $prod->tags);
                    $tagsName = [];
                    foreach ($tagsArray as $tagId) {

                        $tagsName[] = (new TagModel())->findById($tagId)->tag_name ?? '';
                    }
                    $tagName = implode(", ", $tagsName);
                } else {
                    $tagName = !empty($prod->tags) ? ((new TagModel())->findById($prod->tags))->tag_name : "Sem Tag";
                }
                $prod->tag_name = $tagName;
            }
        }
        echo $this->view->render("products", [
            "categoriesWithProds" => (new CategoryModel())->getAllTreeSortCategories(),
            "products" => $products,
            'pagination' => $pager->render()
        ]);
    }


    public function filter($id): void
    {
        $id = $id['id'];
        $likeSearch = "category_id LIKE '%,$id,%' OR category_id LIKE '$id,%' OR category_id LIKE '%,$id' OR category_id = '$id'";
        $row = (new ProductModel)->find("category_id LIKE $likeSearch")->count();

        $page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
        $page = !is_null($page) ? $page : 1;
        $pager = new Paginator(url("produtos/category/{$id}?page="));
        $pager->pager($row, 10, $page);

        $products = (new ProductModel)->find("category_id LIKE $likeSearch")->limit($pager->limit())->offset($pager->offset())->fetch(true);

        if (!empty($products)) {
            foreach ($products as $prod) {
                if (count(explode(',', $prod->category_id)) > 1) {
                    $categoriesArray = explode(',', $prod->category_id);
                    $categoriesName = [];
                    foreach ($categoriesArray as $cat) {
                        $categoriesName[] = (new CategoryModel())->findById($cat)->name;
                    }
                    $categoryName = implode(", ", $categoriesName);
                } else {
                    $categoryName = !empty(((new CategoryModel())->findById($prod->category_id))->name) ? ((new CategoryModel())->findById($prod->category_id))->name : "Sem Categoria";
                }

                $prod->category_name = $categoryName;
            }
        }
        echo $this->view->render("products", [
            "categoriesWithProds" => (new CategoryModel())->getAllTreeSortCategories(),
            "products" => $products,
            'pagination' => $pager->render()
        ]);
    }

    public function new(): void
    {
        $type = filter_input(INPUT_POST, "type");
        if ($type == "new") {
            $this->saveNewProduct();
        }
        $product = new stdClass();
        $product->name = "";
        $product->content = "";
        $product->additional_content = "";
        $product->tags = "";
        $product->code = "";
        $product->specs = "";
        $product->url = "";
        $product->pdf = "";
        $product->cover = "";
        $product->additional_cover = "";
        $product->category_id = "";
        $product->priority = "";
        $product->status = "";

        $allUrls = $this->getAllProdsUrlExistents();
        echo $this->view->render("product", [
            "categories" => $this->getAllCategories(),
            "treeSortCategories" => (new CategoryModel)->getAllTreeSortCategories(),
            "tags" => (new TagModel)->getAllTags(),
            "product" => $product,
            "allUrls" => $allUrls,
            "title" => "Novo Produto",
            "formAction" => url("produtos/adicionar"),
            "type" => "new",
        ]);
    }

    public function edit(array $data)
    {
        $type = filter_input(INPUT_POST, "type");
        if ($type == "edit") {
            $this->saveEditProduct($data);
        }
        $product = $this->getProductById($data['id']);

        $allUrls = $this->getAllProdsUrlExistents();
        echo $this->view->render("product", [
            "categories" => $this->getAllCategories(),
            "treeSortCategories" => (new CategoryModel)->getAllTreeSortCategories(),
            "tags" => (new TagModel)->getAllTags(),
            "product" => $product,
            "allUrls" => $allUrls,
            "title" => "Editar Produto - $product->name",
            "formAction" => url("produtos/editar/{$product->id}"),
            "idButton" => url("galerias/produtos/editar/$product->id"),
            "type" => "edit",
        ]);
    }

    public function search(): void
    {
        $name = filter_var($_POST['s'], FILTER_SANITIZE_SPECIAL_CHARS);
        $products = (new ProductModel())->find("name LIKE '%$name%'")->fetch(true);
        if (!empty($products)) {
            foreach ($products as $prod) {
                if (count(explode(',', $prod->category_id)) > 1) {
                    $categoriesArray = explode(',', $prod->category_id);
                    $categoriesName = [];
                    foreach ($categoriesArray as $cat) {
                        $categoriesName[] = (new CategoryModel())->findById($cat)->name;
                    }
                    $categoryName = implode(", ", $categoriesName);
                } else {
                    $categoryName = !empty(((new CategoryModel())->findById($prod->category_id))->name) ? ((new CategoryModel())->findById($prod->category_id))->name : "Sem Categoria";
                }

                $prod->category_name = $categoryName;
            }
        }
        echo $this->view->render("products", [
            "products" => $products,
            'pagination' => ""
        ]);
    }


    public function deleteImage(array $data)
    {
        $product = $this->getProductById($data['id']);
        $product->cover = null;
        $product->save();
        return $product;
    }

    public function delete(array $data)
    {
        $product = $this->getProductById($data['id']);

        return $product->destroy();
    }

    public function getAddImgByProdId($id)
    {
        $addImgs = (new ProductModel)->find("id = $id", null, 'additional_cover')->fetch(false);
        return $addImgs->additional_cover;
    }
    public function deleteAddImage(array $data)
    {
        $product = $this->getProductById($data['id']);
        $imgToDelete = $this->getAddImgByProdId($data['id']);

        $newAdditionalCover = str_replace($data['img'], 'noimage', $imgToDelete);

        $product->additional_cover = $newAdditionalCover;
        $product->save();
        return $product;
    }


    public function activate(array $data)
    {
        $product = $this->getProductById($data['id']);
        $product->status = "post";
        return $product->save();
    }

    public function getAllProducts()
    {
        $products =  (new ProductModel)->find()->fetch(true);
        foreach ($products as $prod) {
            $prod->url = !empty($prod->url) ? $prod->url : "produto/" . makeUrl($prod->name) . "/$prod->id";
        }
        return $products;
    }

    public function getProductsByCatId($id)
    {
        $likeSearch = "category_id LIKE '%,$id,%' OR category_id LIKE '$id,%' OR category_id LIKE '%,$id' OR category_id = '$id'";
        $products = (new ProductModel)->find("category_id LIKE $likeSearch")->fetch(true);
        foreach ($products as $prod) {
            $prod->url = !empty($prod->url) ? $prod->url : "produto/" . makeUrl($prod->name) . "/$prod->id";
        }
        return $products;
    }

    private function getPagination($products)
    {
        $row = (!is_null($products)) ? count($products) : 0;
        $page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
        $page = !is_null($page) ? $page : 1;
        $pager = new Paginator(url("produtos?page="));
        $pager->pager($row, 5, $page);
        return $pager->render();
    }

    private function getAllCategories()
    {
        return (new CategoryModel())->find()->fetch(true);
    }

    public function getProductById(int $id)
    {
        return (new ProductModel())->findById($id);
    }

    private function saveNewProduct()
    {
        $pdf = new File(UPLOAD_FOLDER . 'product', 'files', false);
        $img = new Image(UPLOAD_FOLDER . 'product', 'images', false);

        $newProduct = new ProductModel();
        $newProduct->name = $_POST['name'];

        if (!empty($_POST['url'])) {
            $urlProcess = $_POST['url'];
            if (strpos($urlProcess, "/") == 0) {
                $urlProcess = ltrim($urlProcess, '/');
            };
            if (strrpos($urlProcess, "/")) {
                $urlProcess = rtrim($urlProcess, '/');
            };
            $urlProcess = str_replace(" ", "-", $urlProcess);
        } else {
            $urlProcess = "produto/" . makeUrl($newProduct->name);
        }
        $newProduct->url = !empty($urlProcess) ? $urlProcess : null;

        $newProduct->content = isset($_POST['content']) ? $_POST['content'] : '';
        $newProduct->code = !empty($_POST['code']) ? $_POST['code'] : null;
        $newProduct->specs = !empty($_POST['specs']) ? $_POST['specs'] : null;
        $newProduct->tags = !empty($_POST['tags']) ? $_POST['tags'] : '';
        $newProduct->category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : exit;
        $newProduct->status = $_POST['status'];

        if (!empty($_FILES['cover']['name'])) {
            try {
                $upload = $img->upload($_FILES['cover'], $_POST['name']);
                $newProduct->cover =  $upload;
            } catch (Exception $e) {
                echo "<p>(!) {$e->getMessage()}</p>";
            }
        }

        if (!empty($_FILES['pdf']['name'])) {
            try {
                $upload = $pdf->upload($_FILES['pdf'], $_POST['name']);
                $newProduct->pdf =  $upload;
            } catch (Exception $e) {
                echo "<p>(!) {$e->getMessage()}</p>";
            }
        }

        $newProduct->priority = !empty($_POST['priority']) ? intval($_POST['priority']) : 99;

        // === conteudo adicional ===
        if (count($_POST['additional_content']) > 0) {
            $textToAdd = join('%#%', $_POST['additional_content']);
            $newProduct->additional_content = $textToAdd ?? '';
        }
        // === upload de imagens adicionais ===
        $coversArray = [];
        if (isset($_FILES['additional_cover']) && count($_FILES['additional_cover']['name']) > 0) {
            $files = array_keys($_FILES['additional_cover']);
            $last_key = end($files);

            for ($i = 0; $i < count($_FILES['additional_cover']['name']); $i++) {
                $imgToUpload = [];

                foreach ($_FILES['additional_cover'] as $key => $value) {
                    $imgToUpload[$key] = $value[$i];
                    if ($key == $last_key) {
                        $coversArray[] = $img->upload($imgToUpload, $_POST['name'] . '-' . $i);
                    }
                }
            }
            $imtToAdd = join('%#%', $coversArray);
            $newProduct->additional_cover = $imtToAdd ?? '';
        };

        $productInsert = $newProduct->save();

        if (!empty($newProduct->url)) {
            $this->geraHtaccess($newProduct);
        }

        if (!$productInsert) {
            $response = [
                "success" => false,
                "message" => "Erro ao salvar o produto"
            ];
        } else {
            $response = [
                "success" => true,
                "message" => "Produto salvo com sucesso",
                "galleryButton" => url("galerias/produtos/editar/$newProduct->id"),
                "clearInputs" => true
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    private function saveEditProduct(array $data)
    {
        $pdf = new File(UPLOAD_FOLDER . 'product', 'files', false);
        $img = new Image(UPLOAD_FOLDER . 'product', 'images', false);

        $product = $this->getProductById($data['id']);
        $product->name = $_POST['name'];

        if (!empty($_POST['url'])) {
            $urlProcess = $_POST['url'];
            if (strpos($urlProcess, "/") == 0) {
                $urlProcess = ltrim($urlProcess, '/');
            };
            if (strrpos($urlProcess, "/")) {
                $urlProcess = rtrim($urlProcess, '/');
            };
            $urlProcess = str_replace(" ", "-", $urlProcess);
        } else {
            $urlProcess = "produto/" . makeUrl($product->name);
        }
        $product->url = !empty($urlProcess) ? $urlProcess : null;

        $product->content = isset($_POST['content']) ? $_POST['content'] : '';
        $product->code = !empty($_POST['code']) ? $_POST['code'] : null;
        $product->specs = !empty($_POST['specs']) ? $_POST['specs'] : null;
        $product->tags = !empty($_POST['tags']) ? $_POST['tags'] : '';
        $product->category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : exit;
        $product->status = $_POST['status'];

        if (!empty($_FILES['cover']['name'])) {

            try {
                $upload = $img->upload($_FILES['cover'], $_POST['name']);
                $product->cover =  $upload;
            } catch (Exception $e) {
                echo "<p>(!) {$e->getMessage()}</p>";
            }
        }

        if (!empty($_FILES['pdf']['name'])) {
            try {
                $upload = $pdf->upload($_FILES['pdf'], $_POST['name']);
                $product->pdf =  $upload;
            } catch (Exception $e) {
                echo "<p>(!) {$e->getMessage()}</p>";
            }
        }

        $product->priority = !empty($_POST['priority']) ? intval($_POST['priority']) : 99;

        // === conteudo adicional ===
        if (count($_POST['additional_content']) > 0) {

            foreach ($_POST['additional_content'] as $key => $value) {
                if ($key ==  0) {
                    if (!empty($value)) {
                        $product->additional_content = $value . '%#%';
                    } else {
                        $indexToRemoveImg[] = $key;
                        $product->additional_content = '';
                    }
                } else {
                    if (!empty($value)) {
                        $product->additional_content .= $value . '%#%';
                    } else {
                        $indexToRemoveImg[] = $key;
                        $product->additional_content .= '';
                    }
                }
            }
        }
        // === remover imagens adicionais especificas ===
        if (isset($indexToRemoveImg) && count($indexToRemoveImg) > 0) {
            $imgsCoversExists = $this->getAddImgByProdId($product->id);
            $coverList = explode('%#%', $imgsCoversExists);

            foreach ($indexToRemoveImg as $key => $value) {
                unset($coverList[$value]);
            }

            $editedCovers = '';
            if (count($coverList) > 0) {
                foreach ($coverList as $key => $value) {
                    if (!empty($value)) {
                        $editedCovers .= $value . '%#%';
                    }
                }
            };
            $product->additional_cover = $editedCovers;
        }

        // === upload de imagens adicionais ===
        $coversArray = [];
        if (!empty($_FILES['additional_cover']) && count($_FILES['additional_cover']['name']) > 0) {

            $files = array_keys($_FILES['additional_cover']);
            $last_key = end($files);

            $imgsCoversExists = $this->getAddImgByProdId($product->id);
            $coverList = explode('%#%', $imgsCoversExists);

            foreach($coverList as $key => $coverImgExistent) {
                if (!empty($coverImgExistent)) {
                    $coversArray[$key] = $coverImgExistent;
                }
            }

            foreach ($_FILES['additional_cover']['name'] as $index => $names ) {
                $imgToUpload = [];
                
                foreach ($_FILES['additional_cover'] as $keyName => $value) {
                    $imgToUpload[$keyName] = $value[$index];

                    if ($keyName == $last_key) {
                        $coversArray[$index] = $img->upload($imgToUpload, $_POST['name'] . '-' . $this->uniqidReal());
                    }
                }
            }
            $imtToAdd = join('%#%', $coversArray);
            $product->additional_cover = $imtToAdd ?? '';
        };

        $productInsert = $product->save();

        if (!empty($product->url)) {
            $this->geraHtaccess($product);
        }

        if (!$productInsert) {
            $response = [
                "success" => false,
                "message" => "Erro ao salvar o produto"
            ];
        } else {
            $response = [
                "success" => true,
                "message" => "Produto salvo com sucesso",

            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    private function getAllProdsUrlExistents()
    {
        $allUrls = (new ProductModel())->find(NULL, NULL, "url")->fetch(true);
        return $allUrls;
    }

    private function uniqidReal($lenght = 8) { // Cria uma string aleatória de 8 caracteres par servir de UUID
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }

    private function geraHtaccess($data)
    {
        $htaccessFilePath = __DIR__ . "/../../../.htaccess";

        // Ler o conteúdo atual do arquivo .htaccess
        $currentContent = file_get_contents($htaccessFilePath);

        // Código que você deseja inserir antes do trecho específico
        $newRule1 = "RewriteRule ^$data->url/?$ prod.php?id=$data->id&cat_id=$data->category_id [NC,L]\n";
        $newRule2 = "RewriteRule ^{$data->url}\/([a-z,0-9,_-]+)\/([0-9-]+)\/?$ prod.php?id={$data->id}&cat_id={$data->category_id} [NC,L]\n";

        // Localize o trecho específico onde deseja inserir o novo código
        $searchPattern = "RewriteRule ^([a-z,0-9,A-Z,_-]+)\/?$ $1.php"; // removido " [NC]" para testes

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
