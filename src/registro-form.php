<?php
require_once 'database.php';
require_once 'database-credentials.php';

// Conexión a la base de datos
$database = new Database(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if (empty($_POST['nombre']) || empty($_POST['contraseña']) || empty($_POST['correo'])) {
    die('ERROR: no se ha introducido la información básica en el formulario de registro');
}

session_start();

// Primero vamos a consultar si el correo ya se está usando
$query_mail = "SELECT id FROM usuarios WHERE correo = '{$_POST["correo"]}'";

if (!$database->isEmpty($query_mail)) {
    $_SESSION["loggedin"] = false;
    $_SESSION["message"] = null;
    $_SESSION["error"] = "El correo ya se está usando.";
    header("location: registro.php");
} else {

    $uploaded_photo_filename = "";

    if (!empty($_FILES["foto"]["name"])) {
        $filename = basename($_FILES["foto"]["name"]);
        $file_type = pathinfo($filename, PATHINFO_EXTENSION);

        $allowed_types = array('jpg', 'png', 'jpeg', 'gif');

        if (in_array($file_type, $allowed_types)) {

            $uploaded_photo_filename = md5(uniqid()) . "." . $file_type;
            $destination = "uploads/" . $uploaded_photo_filename;

            // Upload file
            move_uploaded_file($_FILES["foto"]["tmp_name"], $destination);

        } else {
            $_SESSION["loggedin"] = false;
            $_SESSION["message"] = null;
            $_SESSION["error"] = "La fotografía no es un tipo de archivo válido. Ha de ser JPG, JPEG, PNG o GIF.";
            header("location: registro.php");
            exit();
        }

    }

    $query_insert_user = "INSERT INTO usuarios (nombre, contraseña, correo, foto) VALUES ('{$_POST["nombre"]}', '{$_POST["contraseña"]}', '{$_POST["correo"]}', '{$uploaded_photo_filename}')";

    if ($database->insert($query_insert_user)) {
        $_SESSION["loggedin"] = false;
        $_SESSION["message"] = "Usuario registrado. Ya puedes iniciar sesión.";
        $_SESSION["error"] = null;
        header("location: iniciar-sesion.php");
    }
}
