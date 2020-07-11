<?php session_start();?>

<!doctype html>

<?php
require_once '/usr/local/lib/php/vendor/autoload.php';
require_once 'database.php';
require_once 'database-credentials.php';

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

    
        echo $twig->render('editar-comentario.html.twig', ['comentario' => $result_comment]);
        $_SESSION["error"] = null;
        $_SESSION["message"] = null;
   

    } else {
        echo $twig->render('portada.html.twig');
    }
    
    
}



?>