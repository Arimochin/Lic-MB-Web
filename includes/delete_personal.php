<?php 
require_once 'dbh.inc.terap.php';

// Procesar la acción de mover a ficha médica
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = $_POST['dni'];
    $autor = $_POST['autor'];
    $dev = $_POST['dev'];

    // Eliminar imagen de derivacion
    $imagePath = __DIR__ . "/../derMed-images/" . $dev; // Ruta completa del archivo
    if (file_exists($imagePath)) {
        if (unlink($imagePath)) {
            echo "Imagen eliminada correctamente.";
        } else {
            echo "Error al intentar eliminar la imagen.";
        }
    } else {
        echo "La imagen no existe.";
    }

    // Eliminar imagen de derivacion
    $imagePath = __DIR__ . "/../autObS-images/" . $autor; // Ruta completa del archivo
    if (file_exists($imagePath)) {
        if (unlink($imagePath)) {
            echo "Imagen eliminada correctamente.";
        } else {
            echo "Error al intentar eliminar la imagen.";
        }
    } else {
        echo "La imagen no existe.";
    }




    $delete_query = "DELETE FROM DATOS_PERSONALES WHERE DNI = $1";
    // Ejecutar la consulta de eliminación de datos
    $result_delete = pg_query_params($conn, $delete_query, [$dni]);

    // Verificar si hubo un error en la eliminación
    if (!$result_delete) {
        echo "Error en la eliminación: " . pg_last_error($conn);
        exit; // Detener la ejecución si ocurre un error
    }

    // Redirigir para evitar reenvío de formulario
    header("Location: ../dashboard_personal_info.php");
    exit;
}
?>