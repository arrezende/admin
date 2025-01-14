<?php

require __DIR__ . '/vendor/autoload.php';

use CoffeeCode\Router\Router;

$router = new Router(ROOT);

$router->namespace("Source\Admin");

/**
 * ADMIN
 */
$router->get("/", "Dashboard:home");
$router->get("/dash", "Dashboard:home");


/**
 * LOGIN/LOGOUT
 */
$router->post("/login", "User:login");
$router->get("/login", "User:login");
$router->get("/logout", "User:logout");
/**
 * PRODUTOS
 */

$router->group("/produtos");
$router->get("/", "Product:home");
$router->get("/adicionar", "Product:new");
$router->post("/adicionar", "Product:new");
$router->post("/editar/{id}", "Product:edit");
$router->get("/editar/{id}", "Product:edit");
$router->post("/imagem/{id}", "Product:deleteImage");
$router->post("/remover/{id}", "Product:delete");
$router->post("/remadicional/{id}", "Product:deleteAddImage");
// $router->post("/remadicional/{id}", "Product:deleteAddImage");
$router->post("/ativar/{id}", "Product:activate");
$router->get("/category/{id}", "Product:filter");
$router->post("/category/{id}", "Product:filter");

$router->get("/adicionar-multiples", "ProductMassive:multipleManualAdd");
$router->post("/adicionar-multiples", "ProductMassive:multipleManualAdd");


/**
 * Categorias
 */

$router->group("/categorias");
$router->get("/", "Category:home");
$router->get("/adicionar", "Category:new");
$router->post("/adicionar", "Category:new");
$router->get("/editar/{id}", "Category:edit");
$router->post("/editar/{id}", "Category:edit");
$router->post("/imagem/{id}", "Category:deleteImage");
$router->post("/remover/{id}", "Category:delete");
$router->post("/ativar/{id}", "Category:activate");

/**
 * Tags
 */

$router->group("/tags");
$router->get("/", "Tag:home");
$router->post("/adicionar", "Tag:new");
$router->post("/editar/{id}", "Tag:edit");
$router->post("/remover/{id}", "Tag:delete");

/**
 * Tag Manager
 */

$router->group("/tagsmanager");
$router->get("/", "Tagmanager:home");
$router->post("/save", "Tagmanager:save");
$router->post("/delete/{id}", "Tagmanager:delete");

/**
 * GALERIAS
 */

$router->group("/galerias");
$router->get('/', "Gallery:home");
$router->get('/categorias', "Gallery:categories");
$router->get('/categorias/editar/{id}', "Gallery:categoriesEdit");
$router->post('/categorias/atualizar/{id}', "Gallery:categoriesUpdate");

$router->get('/produtos', "Gallery:products");
$router->get('/produtos/editar/{id}', "Gallery:productsEdit");
$router->post('/produtos/atualizar/{id}', "Gallery:productsUpdate");
$router->post('/remover/{id}', "Gallery:delete");

/**
 * BUSCA
 */
$router->group("/busca");
$router->post("/produtos", "Product:search");
$router->post("/categorias", "Category:search");
$router->post("/tags", "Tag:search");



/**
 * IMAGENS
 */
$router->group("/imagem");
$router->get("/uploader/{id}", "Imagem:upload");
$router->post("/uploader/{id}", "Imagem:upload");

/**
 * ERROR
 */
$router->group("ops");
$router->get("/{errcode}", "Dashboard:error");

/**
 * PROCESS
 */
$router->dispatch();

/*
 * Redirect all errors
 */
if ($router->error()) {
  $router->redirect("/ops/{$router->error()}");
}
