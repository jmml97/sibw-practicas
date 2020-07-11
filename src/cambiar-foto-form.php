<?php
require_once 'database.php';
require_once 'database-credentials.php';

// Conexión a la base de datos
$database = new Database(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

session_start();

$uploaded_photo_filename = "";

if (!empty($_FILES["foto"]["name"])) {
    $filename = basename($_FILES["foto"]["name"]);
    $file_type = pathinfo($filename, PATHINFO_EXTENSION);

    $allowed_types = array('jpg', 'png', 'jpeg', 'gif');

    if (in_array($file_type, $allowed_types)) {

        $uploaded_photo_filename = md5(uniqid()) . "." . $file_type;
        $destination = "uploads/" . $uploaded_photo_filename;

        move_uploaded_file($_FILES["foto"]["tmp_name"], $destination);

        $query_photo = "UPDATE usuarios SET foto = '{$uploaded_photo_filename}' WHERE id = '{$_SESSION["id"]}'";
        $database->update($query_photo);

        header("location: perfil.php");

    } else {
        $_SESSION["message"] = null;
        $_SESSION["error"] = "La fotografía no es un tipo de archivo válido. Ha de ser JPG, JPEG, PNG o GIF.";
        header("location: perfil.php");
        exit();
    }

} else {
    $_SESSION["loggedin"] = false;
    $_SESSION["message"] = null;
    $_SESSION["error"] = "No has subido ninguna fotografía";
    header("location: perfil.php");
    exit();
}
