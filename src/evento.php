<?php

session_start();

require_once '/usr/local/lib/php/vendor/autoload.php';
require_once 'database.php';
require_once 'database-credentials.php';

// Conexión a la base de datos
$database = new Database(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// https://stackoverflow.com/questions/13893347/create-ul-and-li-using-a-multidimensional-array-in-php
// Crea una lista <ul> a partir de un array
function make_list($arr)
{
    $return = '<ul style="display: none" id="prohibidas">';
    foreach ($arr as $item) {
        $return .= '<li>' . ($item) . '</li>';
    }
    $return .= '</ul>';
    return $return;
}

$loader = new \Twig\Loader\FilesystemLoader('./templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

$twig->addGlobal('session', $_SESSION);

$event_id = $_GET['id'];
$event_id = $database->escape($event_id);

// Comprobamos si el parámetro event_id es un número
if (is_numeric($event_id)) {

    // Obtención de los eventos de la barra lateral ----
    if ($_SESSION["is_manager"] || $_SESSION["is_superuser"]) {
        $query = "SELECT * FROM eventos LIMIT 3";
    } else {
        $query = "SELECT * FROM eventos WHERE publicado=1 LIMIT 3";
    }
    $results = $database->fetchAll($query);

    // Obtenemos la ruta de una imagen para cada evento
    $images = array();

    foreach ($results as $e) {
        $query_images = "SELECT ruta FROM imagenes WHERE evento=$e->id";
        $result_images = $database->fetchOne($query_images);
        $images[$e->id] = $result_images->ruta;
    }
    //---------

    // Obtenemos los datos necesarios para la página del evento
    if ($_SESSION["is_manager"] || $_SESSION["is_superuser"]) {
        $query_event = "SELECT * FROM eventos WHERE id=$event_id";
    } else {
        $query_event = "SELECT * FROM eventos WHERE id=$event_id AND publicado=1";
    }
    
    $result_event = $database->fetchOne($query_event);

    if ($result_event) {
        $query_comments = "SELECT comentarios.id, comentarios.usuario, comentarios.fecha, comentarios.comentario, usuarios.nombre, usuarios.foto, comentarios.fecha_edicion, comentarios.editado
      FROM comentarios
      INNER JOIN usuarios ON comentarios.usuario = usuarios.id
      WHERE evento=$event_id";
        $result_comments = $database->fetchAll($query_comments);
        $query_words = "SELECT palabra FROM prohibidas";
        $result_words = $database->fetchAll($query_words);
        $query_images = "SELECT ruta FROM imagenes WHERE evento=$event_id";
        $result_images = $database->fetchAll($query_images);

        $query_tags = "SELECT nombre FROM etiquetas WHERE id IN
      (SELECT id_etiqueta FROM eventos_etiquetas WHERE id_evento = $event_id)";
        $result_tags = $database->fetchAll($query_tags);

        // Obtenemos las palabras prohibidas desde la base de datos y las colocamos
        // en una lista oculta
        $words = array();

        foreach ($result_words as $w) {
            $words[] = $w->palabra;
        }
        $words_list = make_list($words);

        echo $twig->render('evento.html.twig', [
            'eventos' => $results,
            'imagenes_rec'=> $images,
            'evento' => $result_event,
            'comentarios' => $result_comments,
            'prohibidas' => $words_list,
            'imagenes' => $result_images,
            'etiquetas' => $result_tags,
        ]);
        $_SESSION["error"] = null;
        $_SESSION["message"] = null;
    } else {
        $_SESSION["error"] = "El 'id' introducido no se corresponde con ningún evento.";
        header("location: index.php");
    }

} else {
    $_SESSION["error"] = "El 'id' introducido no es un número.";
    header("location: index.php");
}
