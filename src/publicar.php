<?php
require_once 'database.php';
require_once 'database-credentials.php';

// Conexión a la base de datos
$database = new Database(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

session_start();

$error = FALSE;

if ($_SESSION["is_manager"] || $_SESSION["is_superuser"]) {
    $event_id = $_GET["id"];

    $query_published_status = "SELECT publicado FROM eventos WHERE id = '{$event_id}'";
    $published_status = $database->fetchOne($query_published_status)->publicado;

    $new_published_status = $published_status == 1 ? 0: 1;

    $query_update_published = "UPDATE eventos SET 
        publicado = '{$new_published_status}'
        WHERE id = '{$event_id}'";

    if ($database->update($query_update_published)) {

        if (!$published_status) {
            $_SESSION["message"] = "Evento publicado";
        } else {
            $_SESSION["message"] = "Evento ocultado";
        }
        
        header("location: evento.php?id=$event_id");

    } else {
        $_SESSION["error"] = "No se ha podido cambiar el estado del evento";
        header("location: evento.php?id=$event_id");
    }
    
} else {
    $_SESSION["error"] = "No tienes permiso para editar eventos.";
    header("location: index.php");
}
