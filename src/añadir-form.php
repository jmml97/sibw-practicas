<?php
require_once 'database.php';
require_once 'database-credentials.php';

// Conexión a la base de datos
$database = new Database(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

session_start();

$file_error_message = "Ha habido un error al cargar los archivos: ";
$file_empty_message = "Los siguientes archivos estaban vacíos: ";
$file_incorrect_message = "Los siguientes archivos no son del formato permitido: ";

$error = FALSE;

if ($_SESSION["is_manager"] || $_SESSION["is_superuser"]) {
    $raw_date = htmlentities($_POST["fecha"]);
    $event_date = date('Y-m-d', strtotime($raw_date));
    $query_insert_event = "INSERT INTO eventos (titulo, organizador, fecha, descripcion) VALUES ('{$_POST["nombre"]}', '{$_POST["organizador"]}', '{$event_date}', '{$_POST["descripción"]}')";

    if ($database->insert($query_insert_event)) {

        $event_id = $database->insert_id();

        // Añadimos las imágenes

        foreach ($_FILES["imágenes"]["tmp_name"] as $index => $tmp_name) {

            $filename = basename($_FILES['imágenes']['name'][$index]);

            if (!empty($_FILES["imágenes"]["error"][$index])) {
                // some error occured with the file in index $index
                // yield an error here
                $file_error_message = $file_error_message . $filename . ", ";
                $error = TRUE;
            }

            // check whether it's not empty, and whether it indeed is an uploaded file
            if (!empty($tmp_name) && is_uploaded_file($tmp_name)) {
                // the path to the actual uploaded file is in $_FILES[ 'image' ][ 'tmp_name' ][ $index ]
                // do something with it:

                $file_type = pathinfo($filename, PATHINFO_EXTENSION);

                $allowed_types = array('jpg', 'png', 'jpeg', 'gif');

                if (in_array($file_type, $allowed_types)) {
                    $uploaded_image_filename = md5(uniqid()) . "." . $file_type;
                    $destination = "uploads/" . $uploaded_image_filename;

                    move_uploaded_file($tmp_name, $destination);

                    $date = date('Y-m-d H:i:s');

                    $query_insert_image = "INSERT INTO imagenes (evento, fecha, ruta) VALUES ('{$event_id}', '{$date}', '{$destination}')";
                    $database->insert($query_insert_image);

                } else {
                    $file_incorrect_message = $file_incorrect_message . $filename . ", ";
                    $error = TRUE;
                }

            } else {
                $file_empty_message = $file_empty_message . $filename . ", ";
                $error = TRUE;
            }
        }

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
        header("location: añadir.php");

    } else {
        $_SESSION["error"] = "No se ha podido crear el evento en la base de datos";
        header("location: añadir.php");
    }

    if ($error) {
        $_SESSION["error"] = $file_error_message . "\n" . $file_empty_message . "\n" . $file_incorrect_message . ".";
    }
    
} else {
    $_SESSION["error"] = "No tienes permiso para añadir eventos.";
    header("location: index.php");
}
