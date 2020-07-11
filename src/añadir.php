<?php session_start();?>

<!doctype html>

<?php
require_once '/usr/local/lib/php/vendor/autoload.php';
require_once 'database.php';
require_once 'database-credentials.php';

$loader = new \Twig\Loader\FilesystemLoader('./templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

$twig->addGlobal('session', $_SESSION);

if ($_SESSION["is_manager"] || $_SESSION["is_superuser"]) {
    echo $twig->render('añadir.html.twig');
    $_SESSION["error"] = NULL;
    $_SESSION["message"] = NULL;
} else {
    echo $twig->render('portada.html.twig');
}


?>