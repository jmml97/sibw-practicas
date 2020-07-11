<?php
require_once 'database.php';
require_once 'database-credentials.php';

// Conexión a la base de datos
$database = new Database(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if (empty($_POST['correo']) || empty($_POST['contraseña'])) {
    die('ERROR: no se ha introducido la información básica en el formulario de registro');
}

session_start();

$query_user = "SELECT * FROM usuarios WHERE correo = '{$_POST["correo"]}'";
$result_user = $database->fetchOne($query_user);
if ($result_user->contraseña === $_POST['contraseña']) {

    // Store data in session variables
    $_SESSION["loggedin"] = true;
    $_SESSION["id"] = $result_user->id;
    $_SESSION["name"] = $result_user->nombre;
    $_SESSION["error"] = null;
    $_SESSION["message"] = null;
    $_SESSION["is_moderator"] = $result_user->es_moderador;
    $_SESSION["is_manager"] = $result_user->es_gestor;
    $_SESSION["is_superuser"] = $result_user->es_superusuario;

    // Redirect user to welcome page
    header("location: index.php");
} else {
    $_SESSION["loggedin"] = false;
    $_SESSION["error"] = "Los datos de inicio de sesión son incorrectos.";
    $_SESSION["message"] = null;
    header("location: iniciar-sesion.php");
}
