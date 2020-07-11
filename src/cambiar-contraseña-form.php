<?php
require_once 'database.php';
require_once 'database-credentials.php';

// Conexión a la base de datos
$database = new Database(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

session_start();

if (empty($_POST['contraseña']) || empty($_POST['contraseña-nueva']) || empty($_POST['contraseña-nueva-bis'])) {
    die('ERROR: no se ha introducido la información básica en el formulario de registro');
}

$query_user = "SELECT id, nombre, contraseña FROM usuarios WHERE id = '{$_SESSION["id"]}'";
$result_user = $database->fetchOne($query_user);
if ($result_user->contraseña === $_POST['contraseña']) {

    if ($_POST['contraseña-nueva'] === $_POST['contraseña-nueva-bis']) {
        $query_password = "UPDATE usuarios SET contraseña = '{$_POST["contraseña-nueva"]}' WHERE id = '{$_SESSION["id"]}'";
        $database->update($query_password);
        header("location: perfil.php");
    } else {
        die('ERROR: las nuevas contraseñas no coinciden');
    }
} else {
    die('ERROR: la antigua contraseña no es correcta');
}
