<?php
require_once '/usr/local/lib/php/vendor/autoload.php';
require_once 'database.php';
require_once 'database-credentials.php';

session_start();

$loader = new \Twig\Loader\FilesystemLoader('./templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

$twig->addGlobal('session', $_SESSION);

$comment_id = $_GET['id'];

// Conexión a la base de datos
$database = new Database(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
$comment_id = $database->escape($comment_id);


if (is_numeric($comment_id)) {
    $query_comment = "SELECT * FROM comentarios WHERE id=$comment_id";
    $result_comment = $database->fetchOne($query_comment);

    if ($_SESSION["id"] === $result_comment->usuario || $_SESSION["is_moderator"] || $_SESSION["is_superuser"]) {

        $delete_comment_query = "DELETE FROM comentarios WHERE id = '{$comment_id}'";
        $database->delete($delete_comment_query);
    
        header("location: evento.php?id=$result_comment->evento");
        $_SESSION["error"] = null;
        $_SESSION["message"] = null;
   

    } else {
        header("location: evento.php?id=$result_comment->evento");
    }
    
    
}

?>