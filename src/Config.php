<?php

define("ROOT",  "http://localhost/admin");
define("PDF_PRODUTO", false);
define("URL_PRODUTO", true);
define("PDF_CATEGORIA", false);
define("URL_CATEGORIA", true);
define("SUBTITLE_CATEGORIA", false);
define("CARD_DESC_CATEGORIA", false);
define("UPLOAD_FOLDER", "uploads/");

function url(string $uri = null): string
{
    if ($uri) {
        // Verifica se o valor passado começa com "/"
        if (substr($uri, 0, 1) !== '/') {
            $uri = '/' . $uri;
        }
        return ROOT . $uri;
    }

    return ROOT;
}

//BANCO

define("DATA_LAYER_CONFIG", [
    "driver" => "mysql",
    "host" => "localhost",
    "port" => "8003",
    "dbname" => "db",
    "username" => "root",
    "passwd" => "",
    "options" => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
]);

include __DIR__ . "/Stdfunctions.php";
