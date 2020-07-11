<?php session_start(); ?>

<!doctype html>

<?php
require_once '/usr/local/lib/php/vendor/autoload.php';
require_once 'database.php';

$loader = new \Twig\Loader\FilesystemLoader('./templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

$twig->addGlobal('session', $_SESSION);
echo $twig->render('iniciar-sesion.html.twig');
$_SESSION["error"] = NULL;
$_SESSION["message"] = NULL;
