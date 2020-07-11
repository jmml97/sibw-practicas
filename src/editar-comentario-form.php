<?php
require_once 'database.php';
require_once 'database-credentials.php';

// Conexión a la base de datos
$database = new Database(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

session_start();

$error = FALSE;
$comment_id = $_POST["id_comentario"];
$event_id = $_POST["id_evento"];
$date = date('Y-m-d H:i:s');

if ($_SESSION["id"] === $comment_id || $_SESSION["is_moderator"] || $_SESSION["is_superuser"]) {

    $query_update_comment = "UPDATE comentarios SET 
        comentario = '{$_POST["comentario"]}', 
        fecha_edicion = '{$date}', 
        editado = TRUE 
        WHERE id = '{$comment_id}'";

    if ($database->update($query_update_comment)) {

        $_SESSION["message"] = "Comentario modificado";
        header("location: evento.php?id=$event_id");

    } else {
        $_SESSION["error"] = "No se ha podido editar el comentario";
        header("location: evento.php?id=$event_id");
    }
    
} else {
    $_SESSION["error"] = "No tienes permiso para editar este comentario.";
    header("location: index.php");
}
