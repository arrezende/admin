<?php

function makeUrl($titulo)
{
  $titulo = ltrim($titulo);
  $titulo = rtrim($titulo);

  // Substituição de caracteres acentuados deve vir primeiro
  $comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
  $semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u');
  $titulo = str_replace($comAcentos, $semAcentos, $titulo);

  // Remover caracteres indesejados
  $titulo = preg_replace('/[^a-zA-Z0-9 -]/', "", $titulo);

  // Substituir espaços por hifens
  $titulo = str_replace(" ", "-", $titulo);

  // Remover hifens duplicados
  $titulo = preg_replace('/-+/', "-", $titulo);

  // Converter para minúsculas
  $titulo = strtolower($titulo);

  return $titulo;
}

