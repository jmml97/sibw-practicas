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

$event_id = $_GET['id'];

if ($_SESSION["is_manager"] || $_SESSION["is_superuser"]) {

    // Conexión a la base de datos
    $database = new Database(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $event_id = $database->escape($event_id);

    if (is_numeric($event_id)) {
        $query_event = "SELECT * FROM eventos WHERE id=$event_id";
        $result_event = $database->fetchOne($query_event);
        $query_tags = "SELECT nombre FROM etiquetas WHERE id IN
            (SELECT id_etiqueta FROM eventos_etiquetas WHERE id_evento = $event_id)";
        $result_tags = $database->fetchAll($query_tags);

        $tags = array();

        if (!empty($result_tags)) {
            foreach ($result_tags as $t) {
                $tags[] = $t->nombre;
            }
        }

        $tags_string = implode(",", $tags);

        echo $twig->render('editar.html.twig', ['evento' => $result_event, 'etiquetas' => $tags_string]);
        $_SESSION["error"] = null;
        $_SESSION["message"] = null;
    }

} else {
    echo $twig->render('portada.html.twig');
}

?>