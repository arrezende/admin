<?php

namespace Source\Admin;

use CoffeeCode\Uploader\File;
use CoffeeCode\Uploader\Image;
use CoffeeCode\Paginator\Paginator;
use League\Plates\Engine;
use Source\Models\TagModel;
use Source\Models\ProductModel;
use Source\Session\Login;
use stdClass;

#[\AllowDynamicProperties]
class Tag
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
        $row = (new TagModel())->find()->count();
        $page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
        $page = !is_null($page) ? $page : 1;
        $pager = new Paginator(url("tags?page="));
        $pager->pager($row, 25, $page);

        $tags = (new TagModel())->find()->limit($pager->limit())->offset($pager->offset())->fetch(true);

        echo $this->view->render("tags", [
            'tags' => $tags,
            'pagination' => $pager->render()
        ]);
    }

    public function new(): void
    {
        $type = filter_input(INPUT_POST, "type", FILTER_SANITIZE_STRING);
        if ($type == "new") {
            $this->saveNewTag();
        }
        $tag = new stdClass();
        $tag->tag_name = "";
        $tag->tag_cod = "";
        $tag->url = "";

        echo $this->view->render("tags", [
            "tags" => $tag,
            "formAction" => url("tags/adicionar"),
            "title" => "Nova Tag",
            "type" => "new",
        ]);
    }

    public function edit(array $data): void
    {
        $type = filter_input(INPUT_POST, "type");
        if ($type == "edit") {
            
            $this->saveEditTag($data);
        }

        $tags = $this->getTagById($data['id']);

        echo $this->view->render("tags", [
            "tags" => $tags,
            "formAction" => url("tags/editar/{$tags->id}"),
            "title" => "Editar Tag - $tags->tag_name",
            "type" => "edit",
        ]);
    }

    public function search(): void
    {
        $name = filter_var($_POST['s'], FILTER_SANITIZE_SPECIAL_CHARS);
        $tags = (new TagModel())->find("tag_name LIKE '%$name%' OR tag_cod LIKE '%$name%'")->fetch(true);

        echo $this->view->render("tags", [
            'tags' => $tags,
            "pagination" => NULL
        ]);
    }
    public function delete(array $data)
    {
        $tag = $this->getTagById($data['id']);

        return $tag->destroy();
    }

    private function saveNewTag()
    {

        $newTag = new TagModel();
        $newTag->tag_name = !empty($_POST['tag_name']) ? $_POST['tag_name'] : exit;
        $newTag->tag_cod = !empty($_POST['tag_cod']) ? $_POST['tag_cod'] : '';

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
            $urlProcess = "tag/" . makeUrl($newTag->tag_name);
        }
        $newTag->url = !empty($urlProcess) ? $urlProcess : null;

        $tag = $newTag->save();

        if (!$tag) {
            $response = [
                "success" => false,
                "message" => "Erro ao salvar a Tag"
            ];
        } else {
            if (!empty($newTag->url)) {
                $this->geraHtaccess($newTag);
            }
            $response = [
                "success" => true,
                "message" => "Tag salva com sucesso",
                "reload" => true
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    private function saveEditTag(array $data)
    {

        $editTag = $this->getTagById($data['id']);
        $editTag->tag_name = !empty($_POST['tag_name']) ? $_POST['tag_name'] : exit;
        $editTag->tag_cod = !empty($_POST['tag_cod']) ? $_POST['tag_cod'] : '';

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
            $urlProcess = "tag/" . makeUrl($editTag->tag_name);
        }
        $editTag->url = !empty($urlProcess) ? $urlProcess : null;

        $tag = $editTag->save();

        if (!$tag) {
            $response = [
                "success" => false,
                "message" => "Erro ao salvar a categoria"
            ];
        } else {
            if (!empty($editTag->url)) {
                $this->geraHtaccess($editTag);
            }
            $response = [
                "success" => true,
                "message" => "Categoria salva com sucesso",
                "reload" => true
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    private function getTagById($id) {
        return (new TagModel())->findById($id);
    }

    private function geraHtaccess($data)
    {
        $htaccessFilePath = __DIR__ . "/../../../.htaccess";

        // Ler o conteúdo atual do arquivo .htaccess
        $currentContent = file_get_contents($htaccessFilePath);
        // Código que você deseja inserir antes do trecho específico
        $newRule1 = "RewriteRule ^$data->url/?$ produtos.php?tag_id=$data->id [NC,L]\n";
        $newRule2 = "RewriteRule ^{$data->url}\/([a-z,0-9,_-]+)\/([0-9-]+)\/?$ produtos.php?tag_id={$data->id} [NC,L]\n";

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
