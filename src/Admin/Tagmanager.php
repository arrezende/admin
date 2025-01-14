<?php

namespace Source\Admin;

use CoffeeCode\Uploader\File;
use CoffeeCode\Uploader\Image;
use CoffeeCode\Paginator\Paginator;
use League\Plates\Engine;
use Source\Models\TagmanagerModel;
use Source\Session\Login;
use stdClass;

#[\AllowDynamicProperties]
class Tagmanager
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
        $firstRole = 'active IS NOT NULL AND deleted_at IS NULL ORDER BY id DESC LIMIT 1';
        $tags = (new TagmanagerModel())->find($firstRole)->fetch(false);

        $type = filter_input(INPUT_POST, "type", FILTER_SANITIZE_STRING);

        echo $this->view->render("tagsmanager", [
            'tags' => $tags,
            "formAction" => url("tagsmanager/save"),
            "title" => "Tagmanager",
            // 'pagination' => $pager->render()
        ]);
    }

    public function save(): void
    {
        $firstRole = 'active IS NOT NULL AND deleted_at IS NULL ORDER BY id DESC LIMIT 1';
        $tags = (new TagmanagerModel())->find($firstRole)->fetch(false);

        if (empty($tags)) {
            $this->saveNewTag();
        }

        if (!empty($tags)) {
            $this->saveEditTag($tags->id);
        }

        header('Content-Type: application/json');
        exit;
    }


    private function validate_script_tag($content)
    {
        // valida se a estrutura do script está chegando como string para envitar injeção de codigos
        $allowed_pattern = '/^\s*(<!--.*?-->\s*)*<script\b[^>]*>[\s\S]*?<\/script>(\s*<!--.*?-->)*\s*$/is';
        return preg_match($allowed_pattern, $content);
    }

    private function saveNewTag()
    {
        $newTagmanager = new TagmanagerModel();

        // Valida e salva o script
        if (!empty($_POST['head_value']) && $this->validate_script_tag($_POST['head_value'])) {
            $newTagmanager->head_value = trim($_POST['head_value']); // Salva o script como está, sem aplicar `htmlspecialchars`
        } else {
            exit('Formato de script inválido.');
        }

        // Valida e salva o script
        if (!empty($_POST['body_value']) && $this->validate_script_tag($_POST['body_value'])) {
            $newTagmanager->body_value = trim($_POST['body_value']); // Salva o script como está, sem aplicar `htmlspecialchars`
        } else {
            $newTagmanager->body_value = null;
        }

        $newTagmanager->active = 1;
        $newTagmanager->deleted_at = null;
        // var_dump($newTagmanager);
        // die;
        $tag = $newTagmanager->save();

        if (!$tag) {
            $response = [
                "success" => false,
                "message" => "Erro ao salvar Tagmanager",
            ];
        } else {
            $response = [
                "success" => true,
                "message" => "Tagmanager salvo com sucesso",
                "reload" => true
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    private function saveEditTag($id)
    {
        $editTagmanager = $this->getTagmanById($id);
        // Valida e salva o script
        if (!empty($_POST['head_value']) && $this->validate_script_tag($_POST['head_value'])) {
            $editTagmanager->head_value = trim($_POST['head_value']); // Salva o script como está, sem aplicar `htmlspecialchars`
        } else {
            exit('Formato de script inválido.');
        }

        // Valida e salva o script
        if (!empty($_POST['body_value']) && $this->validate_script_tag($_POST['body_value'])) {
            $editTagmanager->body_value = trim($_POST['body_value']); // Salva o script como está, sem aplicar `htmlspecialchars`
        } else {
            $editTagmanager->body_value = null;
        }

        $editTagmanager->active = 1;
        $editTagmanager->deleted_at = null;

        $tag = $editTagmanager->save();

        if (!$tag) {
            $response = [
                "success" => false,
                "message" => "Erro ao editar a Tagmanager"
            ];
        } else {
            // if (!empty($editTag->url)) {
            //     $this->geraHtaccess($editTag);
            // }
            $response = [
                "success" => true,
                "message" => "Tagmanager editado com sucesso",
                "reload" => true
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    private function getTagmanById($id)
    {
        return (new TagmanagerModel())->findById($id);
    }

    
    public function delete($id)
    {
        echo 'entrou';
        die;
        $tag = $this->getTagmanById($id);
        $tag->head_value = $tag->head_value;
        $tag->body_value = $tag->body_value;
        $tag->active = 0;
        $tag->deleted_at = date('Y-m-d H:i:s');

        if (!$tag) {
            $response = [
                "success" => false,
                "message" => "Erro ao remover a Tagmanager"
            ];
        } else {
            $response = [
                "success" => true,
                "message" => "Tagmanager removida com sucesso",
                "reload" => true
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
