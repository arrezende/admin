<?php

namespace Source\Admin;

// require_once 'Config.php';

use CoffeeCode\DataLayer\Connect;
use League\Plates\Engine;
use PDO;
use Source\Models\CategoryModel;
use Source\Models\TagModel;
use Source\Session\Login;
use stdClass;

#[\AllowDynamicProperties]
class ProductMassive extends Connect
{
    private $db;
    private $view;
    private $dsn;
    private $user;
    private $pass;

    public function __construct()
    {
        $this->dsn = "mysql:dbname=" . DATA_LAYER_CONFIG['dbname'] . ";host=" . DATA_LAYER_CONFIG['host'] . ";";
        $this->user = DATA_LAYER_CONFIG['username'];
        $this->pass = DATA_LAYER_CONFIG['passwd'];
        $this->db = new PDO($this->dsn, $this->user, $this->pass);

        $this->view = new Engine(__DIR__ . "/../../theme");
        Login::requireLogin();
    }

    public function multipleManualAdd()
    {
        $type = filter_input(INPUT_POST, "type");
        if ($type == "newManualSave") {
            return $this->massiveManualSave();
        }
        $product = new stdClass();
        $product->name = "";
        $product->content = "";
        $product->additional_content = "";
        $product->tags = "";
        $product->url = "";
        $product->pdf = "";
        $product->cover = "";
        $product->additional_cover = "";
        $product->category_id = "";
        $product->priority = "";
        $product->status = "";

        // $allUrls = $this->getAllProdsUrlExistents();
        echo $this->view->render("product-massive-record", [
            "categoriesExistents" => (new CategoryModel)->getAllCategories(),
            // "treeSortCategories" => (new CategoryModel)->getAllTreeSortCategories(),
            "tagsExistents" => (new TagModel)->getAllTags(),
            "product" => $product,
            // "allUrls" => $allUrls,
            "title" => "Cadastro massivo manual",
            "formAction" => url("produtos/adicionar-multiples"),
            "type" => "newManualSave",
        ]);
    }

    private function massiveManualSave()
    {
        $catsExistents = (new CategoryModel)->getAllCategories();
        $tagsExistents = (new TagModel)->getAllTags();

        if (isset($_POST['name']) && count($_POST['name']) > 0) {
            for ($i = 0; $i < count($_POST['name']); $i++) {

                $name = !empty($_POST['name'][$i]) ? filter_var($_POST['name'][$i], FILTER_SANITIZE_SPECIAL_CHARS) : null;

                if (!empty($_POST['content'][$i])) {
                    $receivedContent = filter_var($_POST['content'][$i], FILTER_SANITIZE_SPECIAL_CHARS);

                    $receivedContentArray = explode('.', $receivedContent);
                    $content = '';
                    foreach ($receivedContentArray as $contentPart) {
                        $contentPart = trim($contentPart);
                        if (!empty($contentPart)) {
                            $content .= "<p>{$contentPart}</p>";
                        }
                    }
                } else {
                    $content = "<p>Lorem ipsum dolor, sit amet consectetur adipisicing elit.</p>";
                }

                if (!empty($_POST['tags'][$i])) {
                    $tagsReceived = explode(',', filter_var($_POST['tags'][$i], FILTER_SANITIZE_SPECIAL_CHARS));
                    $tags = '';

                    foreach ($tagsExistents as $key => $tagE) {
                        foreach ($tagsReceived as $id => $tag) {
                            if ($tag === $tagE->tag_name) {
                                if (empty($tags)) {
                                    $tags .= $tagE->id;
                                } else {
                                    $tags .= ",{$tagE->id}";
                                }
                            }
                        }
                    }
                } else {
                    $tags = '';
                }

                if (!empty($_POST['url'][$i])) {
                    $urlProcess = $_POST['url'][$i];
                    if (strpos($urlProcess, "/") == 0) {
                        $urlProcess = ltrim($urlProcess, '/');
                    };
                    if (strrpos($urlProcess, "/")) {
                        $urlProcess = rtrim($urlProcess, '/');
                    };
                    $urlProcess = str_replace(" ", "-", $urlProcess);
                } else {
                    $urlProcess = "produto/" . makeUrl($name . "-{$i}");
                }
                $url = !empty($urlProcess) ? $urlProcess : null;

                $cover = '';
                if (!empty($_POST['cover'][$i])) {
                    $cover = filter_var($_POST['cover'][$i], FILTER_SANITIZE_SPECIAL_CHARS);
                } else {
                    if (!empty($url)) {
                        $urlSection = explode('/', $url);
                        $cover = end($urlSection) . ".webp";
                    }
                }

                if (!empty($_POST['category_id'][$i])) {
                    $catsReceived = explode(',', filter_var($_POST['category_id'][$i], FILTER_SANITIZE_SPECIAL_CHARS));
                    $category_id = '';

                    foreach ($catsExistents as $key => $catE) {
                        foreach ($catsReceived as $id => $cat) {
                            if ($cat === $catE->name) {
                                if (empty($category_id)) {
                                    $category_id .= $catE->id;
                                } else {
                                    $category_id .= ",{$catE->id}";
                                }
                            }
                        }
                    }
                } else {
                    exit;
                }

                $priority = $_POST['priority'][$i] > 0 ? $_POST['priority'][$i] : 99;

                $data = [
                    ":name" => $name,
                    ":content" => $content,
                    ":additional_content" => '',
                    ":tags" => $tags,
                    ":url" => $url,
                    ':pdf' => '',
                    ":cover" => $cover,
                    ":additional_cover" => '',
                    ":category_id" => $category_id,
                    ":priority" => $priority,
                    ":status" => 'post',
                ];

                try {
                    $this->db->beginTransaction();

                    $sql = "INSERT INTO products (name, content, additional_content, tags, url, pdf, cover, additional_cover, category_id, priority, status) VALUES (:name, :content, :additional_content, :tags, :url, :pdf, :cover, :additional_cover, :category_id, :priority, :status)";
                    $stmt = $this->db->prepare($sql);
                    $success = $stmt->execute($data);

                    if ($success) {
                        $this->db->commit();

                        if ($i + 1 == count($_POST['name'])) {
                            $response = [
                                "success" => true,
                                "message" => "Produto salvo com sucesso",
                            ];
                            header('Content-Type: application/json');
                            echo json_encode($response);
                        }
                    }
                } catch (\Exception $e) {
                    $this->db->rollBack();
                    throw $e;
                }
            }
        }
    }
}
