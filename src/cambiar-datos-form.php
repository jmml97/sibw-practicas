<?php
require_once 'database.php';
require_once 'database-credentials.php';

// Conexión a la base de datos
$database = new Database(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

session_start();

if (empty($_POST['contraseña'])) {
    die('ERROR: no se ha introducido la información básica en el formulario de registro');
}

$query_user = "SELECT id, nombre, contraseña FROM usuarios WHERE id = '{$_SESSION["id"]}'";
$result_user = $database->fetchOne($query_user);

if ($result_user->contraseña === $_POST['contraseña']) {
    $query_data = "UPDATE usuarios SET nombre = '{$_POST["nombre"]}', correo = '{$_POST["correo"]}' WHERE id = '{$_SESSION["id"]}'";
    $database->update($query_data);
    $_SESSION["name"] = $_POST["nombre"];
    header("location: perfil.php");
} else {
    die('ERROR: la contraseña no es correcta');
}
