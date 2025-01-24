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



     // Obtener y eliminar imágenes asociadas al DNI en el directorio `patient-images`
     $select_images_query = "SELECT src FROM PATIENT_IMAGES WHERE DNI = $1";
     $result_images = pg_query_params($conn, $select_images_query, [$dni]);
 
     if ($result_images) {
         while ($row = pg_fetch_assoc($result_images)) {
             $imageFileName = $row['src'];
             $imagePath = __DIR__ . "/../patient-images/" . $imageFileName;
 
             if (file_exists($imagePath)) {
                 if (!unlink($imagePath)) {
                     echo "Error al intentar eliminar la imagen: $imagePath";
                 }
             } else {
                 echo "La imagen no existe: $imagePath";
             }
         }
         pg_free_result($result_images);
     } else {
         echo "Error al obtener las imágenes asociadas al DNI: " . pg_last_error($conn);
     }








    // Mover datos a FICHA_MEDICA
    $delete_query_img = "DELETE FROM PATIENT_IMAGES WHERE DNI = $1";

    // Ejecutar la consulta de eliminación de datos
    $result_delete_img = pg_query_params($conn, $delete_query_img, [$dni]);

    // Verificar si hubo un error en la eliminación
    if (!$result_delete_img) {
        echo "Error en la eliminación: " . pg_last_error($conn);
        exit; // Detener la ejecución si ocurre un error
    }

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