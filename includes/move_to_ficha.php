<?php 
require_once 'dbh.inc.terap.php';
// Procesar la acción de mover a ficha médica
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = $_POST['dni'];


    // Mover datos a FICHA_MEDICA
    $insert_query = "INSERT INTO FICHA_MEDICA (DNI, firstname, secondname, date_of_birth, adress, phone, os)
                     SELECT DNI, firstname, secondname, date_of_birth, adress, phone, os
                     FROM DATOS_PERSONALES
                     WHERE DNI = $1";
    $delete_query = "DELETE FROM DATOS_PERSONALES WHERE DNI = $1";

    // Ejecutar las consultas
    pg_query_params($conn, $insert_query, [$dni]);
    pg_query_params($conn, $delete_query, [$dni]);

    $result_insert = pg_query_params($conn, $insert_query, [$dni]);
    if (!$result_insert) {
        echo "Error en la inserción: " . pg_last_error($conn);
        exit; // Detener la ejecución si ocurre un error
    }

    $result_delete = pg_query_params($conn, $delete_query, [$dni]);
    if (!$result_delete) {
        echo "Error en la eliminación: " . pg_last_error($conn);
        exit; // Detener la ejecución si ocurre un error
    }

    // Redirigir para evitar reenvío de formulario
    header("Location: ../dashboard.php");
    exit;
}