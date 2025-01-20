<?php
// Incluir el archivo de conexión para el usuario `patient`
require_once 'dbh.inc.patient.php';

// Validar que los datos hayan sido enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar y asignar variables a los datos del formulario
    $dni = filter_var($_POST['DNI'], FILTER_SANITIZE_NUMBER_INT);
    $firstname = filter_var($_POST['firstname'], FILTER_UNSAFE_RAW);
    $secondname = filter_var($_POST['secondname'], FILTER_UNSAFE_RAW);
    $dateofbirth = $_POST['dateofbirth'];
    $adress = filter_var($_POST['adress'], FILTER_UNSAFE_RAW);
    $phone_number = filter_var($_POST['phone-number'], FILTER_UNSAFE_RAW);
    $os = filter_var($_POST['os'], FILTER_UNSAFE_RAW);
    $schedule = $_POST['schedule'];


    //Pasar disponibilidad horaria de array a string para la base
    $scheduleString = implode(',', $schedule);
   
    /*---- Codigo para las imagenes ----*/

    // Imagen Derivacion Medica
    $filenameDerMed = $_FILES["derMed"]["name"];
    $tempnameDerMed = $_FILES["derMed"]["tmp_name"];
    $folderDerMed = __DIR__ . "/../derMed-images/";

    if (!is_dir($folderDerMed)) {
        mkdir($folderDerMed, 0777, true);
    }
    $folderDerMed .= $filenameDerMed;
    if (move_uploaded_file($tempnameDerMed, $folderDerMed)){
        echo "<h3>&nbsp; Imagen subida correctamente!</h3>";
    } else {
        echo "<h3>&nbsp; Falló en subirse la imagen!</h3>";
    }

    // Imagen Autorizacion Obra Social
    $filenameAutObS = $_FILES["autObS"]["name"];
    $tempnameAutObS = $_FILES["autObS"]["tmp_name"];
    $folderAutObS = __DIR__ . "/../autObS-images/";

    if (!is_dir($folderAutObS)) {
        mkdir($folderAutObS, 0777, true);
    }
    $folderAutObS .= $filenameAutObS;
    if (move_uploaded_file($tempnameAutObS, $folderAutObS)){
        echo "<h3>&nbsp; Imagen subida correctamente!</h3>";
    } else {
        echo "<h3>&nbsp; Falló en subirse la imagen!</h3>";
    }

    
    // Asegurarse de que la conexión esté activa
    if ($conn) {
        // Preparar la consulta de inserción
        $query = "INSERT INTO DATOS_PERSONALES (dni, firstname, secondname, date_of_birth, adress, phone, os, schedule, dev, autor)
                  VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)"; // Asegurarse de tener todos los 8 parámetros

        // Ejecutar la consulta con parámetros
        $result = pg_query_params($conn, $query, [
            $dni,
            $firstname,
            $secondname,
            $dateofbirth,
            $adress,
            $phone_number,
            $os,
            $scheduleString,
            $filenameDerMed,
            $filenameAutObS
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