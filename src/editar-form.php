<?php
require_once 'database.php';
require_once 'database-credentials.php';

// Conexión a la base de datos
$database = new Database(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

session_start();

$error = FALSE;

if ($_SESSION["is_manager"] || $_SESSION["is_superuser"]) {
    $raw_date = htmlentities($_POST["fecha"]);
    $event_date = date('Y-m-d', strtotime($raw_date));

    $event_id = $_POST["id"];

    $query_update_event = "UPDATE eventos SET 
        titulo = '{$_POST["nombre"]}', 
        organizador = '{$_POST["organizador"]}', 
        fecha = '{$_POST["fecha"]}', 
        descripcion = '{$_POST["descripción"]}' 
        WHERE id = '{$event_id}'";

    if ($database->update($query_update_event)) {

        $delete_tags_query = "DELETE FROM eventos_etiquetas WHERE id_evento = '{$event_id}'";
        $database->delete($delete_tags_query);

        // Añadimos las etiquetas

        $tags = $_POST["etiquetas"];
        $tags_array = explode(",", $tags);
        
        foreach ($tags_array as $index => $tag) {
            
            $query_tag = "SELECT id, nombre FROM etiquetas WHERE nombre = '{$tag}'";
            $tag_id = NULL;

            // Vemos si el tag existe
            if ($database->isEmpty($query_tag)) {
                // Si no existe lo creamos y obtenemos su id
                $query_insert_tag = "INSERT INTO etiquetas (nombre) VALUES ('{$tag}')";
                if ($database->insert($query_insert_tag)) {
                    $tag_id = $database->insert_id();
                }
            } else {
                // Si el tag existe, tomamos su id
                $result_tag = $database->fetchOne($query_tag);
                $tag_id = $result_tag->id;
            }

            $query_insert_event_tag = "INSERT INTO eventos_etiquetas (id_evento, id_etiqueta) VALUES ('{$event_id}', '{$tag_id}')";
            $database->insert($query_insert_event_tag);
        }

        $_SESSION["message"] = "Evento añadido";
        header("location: evento.php?id=$event_id");

    } else {
        $_SESSION["error"] = "No se ha podido editar el evento";
        header("location: evento.php?id=$event_id");
    }
    
} else {
    $_SESSION["error"] = "No tienes permiso para editar eventos.";
    header("location: index.php");
}
