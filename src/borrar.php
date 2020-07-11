<?php
require_once '/usr/local/lib/php/vendor/autoload.php';
require_once 'database.php';
require_once 'database-credentials.php';

session_start();

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

        $delete_event_query = "DELETE FROM eventos WHERE id = '{$event_id}'";
        $database->delete($delete_event_query);

        header("location: index.php");
        $_SESSION["error"] = null;
        $_SESSION["message"] = null;
    }

} else {
    header("location: index.php");
}

?>