<?php 
require_once 'dbh.inc.terap.php';

// Procesar la acción de mover a ficha médica
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = $_POST['dni'];

    // Mover datos a FICHA_MEDICA
    $delete_query = "DELETE FROM FICHA_MEDICA WHERE DNI = $1";

    // Ejecutar la consulta de eliminación de datos
    $result_delete = pg_query_params($conn, $delete_query, [$dni]);

    // Verificar si hubo un error en la eliminación
    if (!$result_delete) {
        echo "Error en la eliminación: " . pg_last_error($conn);
        exit; // Detener la ejecución si ocurre un error
    }

    // Redirigir para evitar reenvío de formulario
    header("Location: ../dashboard_medical_info.php");
    exit;
}
?>