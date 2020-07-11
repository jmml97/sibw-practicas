<?php
require_once 'database.php';
require_once 'database-credentials.php';

session_start();

if ($_SESSION["is_superuser"]) {
    $database = new Database(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    switch ($_POST["formulario"]) {
        case 'moderador':
            $bool = !$_POST["es_moderador"];
            $query = "UPDATE usuarios SET es_moderador = '{$bool}' WHERE id = '{$_POST["id"]}'";
            break;
        case 'gestor':
            $bool = !$_POST["es_gestor"];
            $query = "UPDATE usuarios SET es_gestor = '{$bool}' WHERE id = '{$_POST["id"]}'";
            break;
        case 'superusuario':
            $bool = !$_POST["es_superusuario"];
            $query = "UPDATE usuarios SET es_superusuario = '{$bool}' WHERE id = '{$_POST["id"]}'";
            break;
        
        default:
            break;
    }

    
    $database->update($query);

    header("location: usuarios.php");
}
