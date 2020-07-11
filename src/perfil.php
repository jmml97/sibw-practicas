<?php session_start();?>

<!doctype html>

<?php
require_once '/usr/local/lib/php/vendor/autoload.php';
require_once 'database.php';
require_once 'database-credentials.php';

// Conexión a la base de datos
$database = new Database(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

$loader = new \Twig\Loader\FilesystemLoader('./templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

// Obtención de los eventos
$query = "SELECT * FROM usuarios WHERE id = '{$_SESSION["id"]}'";
$user = $database->fetchOne($query);

$twig->addGlobal('session', $_SESSION);
echo $twig->render('perfil.html.twig', ['usuario' => $user]);
?>