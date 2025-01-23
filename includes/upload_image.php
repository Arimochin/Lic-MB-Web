<?php
// Incluir el archivo de conexión para el usuario `patient`
require_once 'dbh.inc.terap.php';
echo "hola";

// Validar que los datos hayan sido enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar y asignar variables a los datos del formulario
    $dni = filter_var($_POST['dni'], FILTER_SANITIZE_NUMBER_INT);
    /*---- Codigo para las imagenes ----*/

    // Imagen Derivacion Medica
    $filenameimg = $_FILES["image"]["name"];
    $tempnameimg = $_FILES["image"]["tmp_name"];
    $folderimg = __DIR__ . "/../patient-images/";

    if (!is_dir($folderimg)) {
        mkdir($folderimg, 0777, true);
    }
    $folderimg .= $filenameimg;
    if (move_uploaded_file($tempnameimg, $folderimg)){
        echo "<h3>&nbsp; Imagen subida correctamente!</h3>";
    } else {
        echo "<h3>&nbsp; Falló en subirse la imagen!</h3>";
    }

    // Asegurarse de que la conexión esté activa
    if ($conn) {

        // Calcular el número de imagen
        $img_query = "SELECT COALESCE(COUNT(IMG), 0) + 1 AS next_img FROM PATIENT_IMAGES WHERE DNI = $1";
        $img_result = pg_query_params($conn, $img_query, [$dni]);

        if ($img_result) {
            $row = pg_fetch_assoc($img_result);
            $next_img = $row['next_img'];
            // Insertar registro en la base de datos
            $insert_query = "INSERT INTO PATIENT_IMAGES (DNI, IMG, src) VALUES ($1, $2, $3)";
            $result_insert = pg_query_params($conn, $insert_query, [$dni, $next_img, $filenameimg]);
            // Comprobar el resultado
            if ($result_insert) {
                echo "Datos enviados correctamente.";
            } else {
                echo "Error al insertar datos: " . pg_last_error($conn);
            }
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