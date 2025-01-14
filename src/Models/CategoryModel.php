<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;
use Source\Admin\Category;

class CategoryModel extends DataLayer
{

    public function __construct()
    {
        parent::__construct("category", ["name"]);

        //url, description, cover, father_id, pdf, gallery_id, created_at e updated_at
    }

    public function getAllCategories()
    {
        $categories =  $this->find()->fetch(true);
        foreach ($categories as $cat) {
            $cat->url = !empty($cat->url) ? $cat->url : "categoria/" . makeUrl($cat->name) . "/$cat->id";
        }
        return $categories;
    }

    public function getAllMainCategories()
    {
        // Pega apenas as categorias
        $categories =  $this->find("father_id = :father_id", "father_id=0")->fetch(true);
        if (!empty($categories)) {
            foreach ($categories as $cat) {
                $cat->url = !empty($cat->url) ? $cat->url : "categoria/" . makeUrl($cat->name) . "/$cat->id/$cat->father_id";
            }
        }
        return $categories;
    }

    public function getAllTreeSortCategories()
    {
        // Estrutura recursiva para estruturar a arvore completa de categoria e subcategorias com um parametro adicional de endentação.
        $categories =  $this->getAllMainCategories();
        $treeSortCategories = [];
        if (!empty($categories)) {
            foreach ($categories as $cat) {
                $treeSortCategories[] = $cat;
                $this->getAllSubcategoriesRecursive($cat, $treeSortCategories, 1);
            }
        }

        return $treeSortCategories;
    }

    private function getAllSubcategoriesRecursive($category, &$treeSortCategories, $level)
    {
        $subcategories = $this->getAllCategoriesByFatherId(intval($category->id));
        $subCategoryItems = !empty($subcategories) ? count($subcategories) : 0;

        if ($subCategoryItems > 0) {
            $indentation = str_repeat('--| ', $level * 1);

            for ($i = 0; $i < $subCategoryItems; $i++) {
                $subcategories[$i]->indentation = $indentation;
                $subCategory = $subcategories[$i];
                $subCategory->name = $subCategory->name;
                $treeSortCategories[] = $subCategory;
                $this->getAllSubcategoriesRecursive($subCategory, $treeSortCategories, $level + 1);
            }
        }
    }

    public function getAllCategoriesByFatherId(int $father_id)
    {
        return $this->find("father_id = :father_id", "father_id=$father_id")->fetch(true);
    }
}
