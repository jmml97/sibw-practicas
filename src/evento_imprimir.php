<!doctype html>

<?php
  require_once '/usr/local/lib/php/vendor/autoload.php';
  $loader = new \Twig\Loader\FilesystemLoader('./templates'); 
  $twig = new \Twig\Environment($loader, [
    'cache' => false,
  ]);
  echo $twig->render('evento_imprimir.html.twig');
?>