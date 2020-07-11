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

$search = $_POST["búsqueda"];
$search = $database->escape($search);

// Obtención de los eventos
$query = "SELECT * FROM eventos WHERE (titulo LIKE '%" . $search . "%') OR (descripcion LIKE '%" . $search . "%')";
$results = $database->fetchAll($query);

// Obtenemos la ruta de una imagen para cada evento
$images = array();

if ($results) {
    foreach ($results as $e) {
        $query_images = "SELECT ruta FROM imagenes WHERE evento=$e->id";
        $result_images = $database->fetchOne($query_images);
        $images[$e->id] = $result_images->ruta;
    }
}



$twig->addGlobal('session', $_SESSION);
echo $twig->render('buscar.html.twig', ['eventos' => $results, 'imagenes' => $images]);
?>