<?php

namespace Source\Admin;

use League\Plates\Engine;
use Source\Models\CategoryModel;
use CoffeeCode\Uploader;

class Imagem{

    private $file;
    private $maxSize;
    private $folder;
    private $allowTypes = [
        "image/jpeg",
        "image/png",
        "image/gif",
    ];

    public function setFile($file) {
        $this->file = $file;
        return $this;
    }



    public function setMaxSize($maxSize) {
        $this->maxSize = $maxSize;
        return $this;
    }




    public function setFolder(string $folder)
    {
        $this->folder = "uploads/".$folder;
        return $this;
    }

    public function upload()
    {
        foreach($this->file as $file){
            if(in_array($file['type'], $this->allowTypes)){
                try {
                    $ds = DIRECTORY_SEPARATOR;
                    print_r($file);
                    $tempFile = $file['tmp_name'];          //3             
                    $targetPath = dirname( __FILE__,3 ) . $ds. $this->folder . $ds;  //4
                    $targetFile =  $targetPath. $file['name'];  //5
                    move_uploaded_file($tempFile,$targetFile); //6
                    return true;
                } catch (Exception $e) {
                    echo "<p>(!) {$e->getMessage()}</p>";
                    return false;
                }

            }

        }

        
        // $id = $data['id'];
        

        // $category = (new CategoryModel)->findById($id)->fetch();

        // $ds          = DIRECTORY_SEPARATOR;  //1
 
        // if (!empty($files)) {
        //     try {
        //         $tempFile = $_FILES['file']['tmp_name'];          //3             
            
        //         $targetPath = dirname( __FILE__,3 ) . $ds. $this->folder . $ds;  //4
                
        //         $targetFile =  $targetPath. $_FILES['file']['name'];  //5
            
        //         move_uploaded_file($tempFile,$targetFile); //6

        //         return true;
        //     } catch (Exception $e) {
        //         echo "<p>(!) {$e->getMessage()}</p>";
        //         return false;
        //     }
            
        // }
       
        // print_r([$category,$_POST,$_GET,$_FILES]);
        // return json_encode($category);
    }

    public function new(): void
    {
        echo $this->view->render("product-new");
    }
}