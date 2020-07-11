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
if ($_SESSION["is_manager"] || $_SESSION["is_superuser"]) {
    $query = "SELECT * FROM eventos";
} else {
    $query = "SELECT * FROM eventos WHERE publicado=1";
}
$results = $database->fetchAll($query);

// Obtenemos la ruta de una imagen para cada evento
$images = array();

foreach ($results as $e) {
    $query_images = "SELECT ruta FROM imagenes WHERE evento=$e->id";
    $result_images = $database->fetchOne($query_images);
    $images[$e->id] = $result_images->ruta;
}

$twig->addGlobal('session', $_SESSION);
echo $twig->render('portada.html.twig', ['eventos' => $results, 'imagenes' => $images]);
$_SESSION["error"] = null;
$_SESSION["message"] = null;
?>