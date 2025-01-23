<?php

function getPatientImages($conn, $dni) {
    $query = "SELECT src FROM PATIENT_IMAGES WHERE DNI = $1";
    $result = pg_query_params($conn, $query, array($dni));

    if (!$result) {
        echo "Error en la consulta: " . pg_last_error($conn);
        return [];
    }

    return pg_fetch_all($result); // Asegúrate de que esto devuelva todas las filas
}

require_once 'includes/dbh.inc.terap.php';
$query = "SELECT *, extract(years from AGE(date_of_birth)) as age FROM FICHA_MEDICA";
// Inicializar variables
$search = '';
// Procesar búsqueda si se envía el formulario
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $query .= " WHERE LOWER(firstname) LIKE LOWER('%$search%') 
                OR LOWER(secondname) LIKE LOWER('%$search%') 
                OR CAST(dni AS TEXT) LIKE '%$search%'";
}

$result = pg_query($conn,$query);

// Procesar actualización de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_dni'])) {
    $dni = $_POST['update_dni'];
    $firstname = $_POST['firstname'];
    $secondname = $_POST['secondname'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $os = $_POST['os'];
    $schedule = $_POST['schedule'];
    $diagnosis = $_POST['diagnosis'];
    $evaluation = $_POST['evaluation'];
    $surgery_date = !empty($_POST['surgery_date']) ? $_POST['surgery_date'] : null; // Manejo de fechas nulas
    $discharge_date = !empty($_POST['discharge_date']) ? $_POST['discharge_date'] : null; // Manejo de fechas nulas
    $observations = $_POST['observations'];

    $update_query = "UPDATE FICHA_MEDICA SET firstname = $1, secondname = $2, adress = $3, phone = $4, os = $5, schedule = $6,
                        diagnosis = $7, evaluation = $8, surgery_date = $9, discharge_date = $10, observations = $11 WHERE dni = $12";
    $update_result = pg_query_params($conn, $update_query, array($firstname, $secondname, $address, $phone, $os, $schedule, $diagnosis, 
                                        $evaluation, $surgery_date, $discharge_date, $observations, $dni));

    if ($update_result) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit;
    } else {
        $error = "Error al actualizar los datos.";
    }
}

?>


<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Lic MB</title> <!-- nombre temporal -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <!-- Latest compiled and minified CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Latest compiled JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body class="bg-light">
        <!-- <a href="dashboard_personal_info.php" class="right-corner"> <button class="btn btn-primary">< Ver Datos Personales</button> </a> -->
        <div class="container py-4">
            <!-- Botón derecho -->
            <div class="d-flex justify-content-end mb-3">
                <a href="dashboard_personal_info.php" class="heading">< Ver Datos Personales</a>
            </div>

            <!-- Barra de búsqueda -->
            <form action="" method="get" class="d-flex mb-4">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control me-1" 
                    placeholder="Buscar por nombre, apellido o DNI..." 
                    value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="search-btn">Buscar</button>
            </form>

            <!-- Resultados -->
            <div class="row g-4">
                <?php while($row = pg_fetch_assoc($result)){ ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm">
                        <div class="info-card card-body">
                            <form method="POST" class="mb-3">
                                <!-- Modo Vista -->
                                <div class="view-mode" id="view-mode-<?php echo $row['dni']; ?>">
                                    <h5 class="person-name"><?php echo $row['firstname'] . ' ' . $row['secondname']; ?></h5>
                                    <p class="card-text"><strong>DNI: </strong><?php echo $row['dni']; ?></p>
                                    <!-- <p class="card-text"><strong>Nombre: </strong><?php echo $row['firstname']; ?></p>
                                    <p class="card-text"><strong>Apellido: </strong> <?php echo $row['secondname']; ?></p> -->
                                    <p class="card-text"><strong>Edad: </strong> <?php echo $row['age']; ?></p>
                                    <p class="card-text"><strong>Dirección: </strong><?php echo $row['adress']; ?></p>
                                    <p class="card-text"><strong>Teléfono: </strong><?php echo $row['phone']; ?></p>
                                    <p class="card-text"><strong>Obra Social: </strong><?php echo $row['os']; ?></p>
                                    <p class="card-text"><strong>Disponibilidad Horaria: </strong><?php echo $row['schedule']; ?></p>
                                    <p class="card-text"><strong>Diagnóstico: </strong><?php echo $row['diagnosis']; ?></p>
                                    <p class="card-text"><strong>Evaluación: </strong><?php echo $row['evaluation']; ?></p>
                                    <p class="card-text"><strong>Fecha de Cirugía: </strong><?php echo $row['surgery_date']; ?></p>
                                    <p class="card-text"><strong>Fecha de Alta: </strong><?php echo $row['discharge_date']; ?></p>
                                    <p class="card-text"><strong>Observaciones: </strong><?php echo $row['observations']; ?></p>

                                    <!-- Botones de modales -->
                                    <button type="button" class="img-btn" data-bs-toggle="modal" data-bs-target="#derMedModal-<?php echo $row['dni']; ?>">Derivación Médica</button>
                                    <button type="button" class="img-btn" data-bs-toggle="modal" data-bs-target="#autObSModal-<?php echo $row['dni']; ?>">Autorización Obra Social</button>
                                    
                                    <!-- Modal Derivación Médica -->
                                    <div class="modal fade" id="derMedModal-<?php echo $row['dni']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <img src="derMed-images/<?php echo htmlspecialchars($row['dev'], ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid" alt="Derivación Médica">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal Autorización Obra Social -->
                                    <div class="modal fade" id="autObSModal-<?php echo $row['dni']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <img src="autObS-images/<?php echo htmlspecialchars($row['autor'], ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid" alt="Autorización Obra Social">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-row">
                                        <button type="button" class="edit-btn mt-3" onclick="toggleEdit(<?php echo $row['dni']; ?>)">Editar</button>
                                        <!-- Boton modal cargar -->
                                        <button 
                                            type="button" 
                                            class="btn btn-2xs btn-primary mt-2 img-upload-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#addImageModal-<?php echo $row['dni']; ?>">
                                            <i class="fa fa-upload fa-sm p-0"></i>
                                        </button>
                                    </div>
                                    <!-- Botón para abrir la galería -->
                                    <button type="button" class="img-btn mt-3" data-bs-toggle="modal" data-bs-target="#galleryModal-<?php echo $row['dni']; ?>">
                                        Ver Galería
                                    </button>

                                    <!-- Modal para la galería de imágenes -->
                                    <div class="modal fade" id="galleryModal-<?php echo $row['dni']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Galería de Imágenes - <?php echo $row['firstname'] . ' ' . $row['secondname']; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <?php
                                                        $images = getPatientImages($conn, $row['dni']);
                                                        if ($images) {
                                                            foreach ($images as $image) {
                                                                echo '
                                                                    <div class="col-md-4 mb-3">
                                                                        <a target="_blank" href="patient-images/' . htmlspecialchars($image['src'], ENT_QUOTES, 'UTF-8') . '">
                                                                            <img src="patient-images/' . htmlspecialchars($image['src'], ENT_QUOTES, 'UTF-8') . '" class="img-fluid rounded">
                                                                        </a>
                                                                        <div class="text-center mt-2">Add a description of the image here</div>
                                                                    </div>';
                                                            }
                                                        } else {
                                                            echo '<p class="text-center">No hay imágenes disponibles.</p>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- Modo Edición -->
                                <div class="edit-mode d-none" id="edit-mode-<?php echo $row['dni']; ?>">
                                    <input type="hidden" name="update_dni" value="<?php echo $row['dni']; ?>">
                                    <div class="mb-2">
                                        <label>Nombre</label>
                                        <input type="text" name="firstname" class="form-control" value="<?php echo $row['firstname']; ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label>Apellido</label>
                                        <input type="text" name="secondname" class="form-control" value="<?php echo $row['secondname']; ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label>Dirección</label>
                                        <input type="text" name="address" class="form-control" value="<?php echo $row['adress']; ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label>Teléfono</label>
                                        <input type="text" name="phone" class="form-control" value="<?php echo $row['phone']; ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label>Obra Social</label>
                                        <input type="text" name="os" class="form-control" value="<?php echo $row['os']; ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label>Disponibilidad Horaria</label>
                                        <input type="text" name="schedule" class="form-control" value="<?php echo $row['schedule']; ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label>Diagnóstico</label>
                                        <input type="text" name="diagnosis" class="form-control" value="<?php echo $row['diagnosis']; ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label>Tratamiento</label>
                                        <input type="text" name="evaluation" class="form-control" value="<?php echo $row['evaluation']; ?>" >
                                    </div>
                                    <div class="mb-2">
                                        <label>Fecha de Cirugía</label>
                                        <input type="date" name="surgery_date" class="form-control" value="<?php echo $row['surgery_date']; ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label>Fecha de Alta</label>
                                        <input type="date" name="discharge_date" class="form-control" value="<?php echo $row['discharge_date']; ?>" >
                                    </div>
                                    <div class="mb-2">
                                        <label>Observaciones</label>
                                        <textarea name="observations" class="form-control" rows="5" placeholder="Escribe tus observaciones aquí..."><?php echo htmlspecialchars($row['observations']); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success">Guardar</button>
                                    <button type="button" class="btn btn-secondary" onclick="toggleEdit(<?php echo $row['dni']; ?>)">Cancelar</button>
                                </div>
                            </form>
                            <!-- Boton modal eliminar -->
                            <button type="button" class="btn btn-sm btn-danger float-end" data-bs-toggle="modal" data-bs-target="#confirm"><i class="fa fa-trash-o fa-lg">  </i></button>
                            <!-- Modal Eliminar -->
                            <div class="modal modal-sm fade" id="confirm"  tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">¿Estas seguro que deseas eliminar la informacion?</h5>
                                        </div>
                                        <div class="modal-footer">
                                        <form action="includes/delete_medical.php" method="post" class="mt-3">
                                                <input type="hidden" name="dni" value="<?php echo $row['dni']; ?>">
                                                <input type="hidden" name="autor" value="<?php echo $row['autor']; ?>">
                                                <input type="hidden" name="dev" value="<?php echo $row['dev']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirm">Eliminar</button>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal carga de imagenes-->
                            <div class="modal fade" id="addImageModal-<?php echo $row['dni']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Agregar Imagen para <?php echo $row['firstname'] . ' ' . $row['secondname']; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                </div>
                                                <form action="includes/upload_image.php" method="post" enctype="multipart/form-data">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="dni" value="<?php echo $row['dni']; ?>">
                                                        <div class="mb-3">
                                                            <label for="image" class="form-label">Seleccionar Imagen</label>
                                                            <input type="file" name="image" class="form-control" accept="image/*" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Subir Imagen</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    </div>
                                                    
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>

        <script>
            function toggleEdit(dni) {
                const viewMode = document.getElementById(`view-mode-${dni}`);
                const editMode = document.getElementById(`edit-mode-${dni}`);
                viewMode.classList.toggle('d-none');
                editMode.classList.toggle('d-none');
            }
        </script>
    </body>
</html>