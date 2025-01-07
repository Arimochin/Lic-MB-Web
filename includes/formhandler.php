<?php
// Incluir el archivo de conexión para el usuario `patient`
require_once 'dbh.inc.patient.php';

// Validar que los datos hayan sido enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar y asignar variables a los datos del formulario
    $dni = filter_var($_POST['DNI'], FILTER_SANITIZE_NUMBER_INT);
    $firstname = filter_var($_POST['firstname'], FILTER_UNSAFE_RAW);
    $secondname = filter_var($_POST['secondname'], FILTER_UNSAFE_RAW);
    $adress = filter_var($_POST['adress'], FILTER_UNSAFE_RAW);
    $phone_number = filter_var($_POST['phone-number'], FILTER_UNSAFE_RAW);
    $os = filter_var($_POST['os'], FILTER_UNSAFE_RAW);


    // Asegurarse de que la conexión esté activa
    if ($conn) {
        // Preparar la consulta de inserción
        $query = "INSERT INTO DATOS_PERSONALES (dni, firstname, secondname, adress, phone, os)
                  VALUES ($1, $2, $3, $4, $5, $6 )"; // Asegurarse de tener todos los 8 parámetros

        // Ejecutar la consulta con parámetros
        $result = pg_query_params($conn, $query, [
            $dni,
            $firstname,
            $secondname,
            $adress,
            $phone_number,
            $os
        ]);

        // Comprobar el resultado
        if ($result) {
            echo "Datos enviados correctamente.";
        } else {
            echo "Error al insertar datos: " . pg_last_error($conn);
        }
    } else {
        echo "Error de conexión a la base de datos.";
    }
} else {
    echo "Método de solicitud no permitido.";
}

// Cerrar la conexión
pg_close($conn);
?>