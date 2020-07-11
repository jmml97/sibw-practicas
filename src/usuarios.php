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

$users = array();

if ($_SESSION["is_superuser"]) {
    // Conexión a la base de datos
    $database = new Database(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    $query = "SELECT * FROM usuarios";
    $users = $database->fetchAll($query);
}

$twig->addGlobal('session', $_SESSION);
echo $twig->render('usuarios.html.twig', ['usuarios' => $users]);

?>