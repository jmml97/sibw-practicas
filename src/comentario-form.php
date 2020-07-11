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

if ($_SESSION["loggedin"]) {
    $date = date('Y-m-d H:i:s');
    $id = $_POST["id"];
    $query_insert_comment = "INSERT INTO comentarios (usuario, fecha, comentario, evento) 
        VALUES ('{$_SESSION["id"]}', '{$date}','{$_POST["comentario"]}', '{$id}')";

    if ($database->insert($query_insert_comment)) {

        $_SESSION["message"] = "Comentario añadido";
        header("location: evento.php?id=$id");

    } else {
        $_SESSION["error"] = "No se ha podido crear el comentario en la base de datos";
        header("location: evento.php?id=$id");
    }
    
} else {
    $_SESSION["error"] = "Tienes que iniciar sesión para comentar.";
    header("location: evento.php?id=$id");
}
