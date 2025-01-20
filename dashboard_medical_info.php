<?php
require_once 'includes/dbh.inc.terap.php';
$query = "SELECT * FROM FICHA_MEDICA";
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
    $treatment = $_POST['treatment'];
    $surgery_date = !empty($_POST['surgery_date']) ? $_POST['surgery_date'] : null; // Manejo de fechas nulas
    $discharge_date = !empty($_POST['discharge_date']) ? $_POST['discharge_date'] : null; // Manejo de fechas nulas

    $update_query = "UPDATE FICHA_MEDICA SET firstname = $1, secondname = $2, adress = $3, phone = $4, os = $5, schedule = $6,
                        diagnosis = $7, treatment = $8, surgery_date = $9, discharge_date = $10 WHERE dni = $11";
    $update_result = pg_query_params($conn, $update_query, array($firstname, $secondname, $address, $phone, $os, $schedule, $diagnosis, $treatment, $surgery_date, $discharge_date, $dni));

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
                                    <p class="card-text"><strong>Fecha de Nacimiento: </strong> <?php echo $row['date_of_birth']; ?></p>
                                    <p class="card-text"><strong>Dirección: </strong><?php echo $row['adress']; ?></p>
                                    <p class="card-text"><strong>Teléfono: </strong><?php echo $row['phone']; ?></p>
                                    <p class="card-text"><strong>Obra Social: </strong><?php echo $row['os']; ?></p>
                                    <p class="card-text"><strong>Disponibilidad Horaria: </strong><?php echo $row['schedule']; ?></p>
                                    <p class="card-text"><strong>Diagnóstico: </strong><?php echo $row['diagnosis']; ?></p>
                                    <p class="card-text"><strong>Tratamiento: </strong><?php echo $row['treatment']; ?></p>
                                    <p class="card-text"><strong>Fecha de Cirugía: </strong><?php echo $row['surgery_date']; ?></p>
                                    <p class="card-text"><strong>Fecha de Alta: </strong><?php echo $row['discharge_date']; ?></p>

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

                                    <button type="button" class="edit-btn mt-3" onclick="toggleEdit(<?php echo $row['dni']; ?>)">Editar</button>

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
                                        <input type="text" name="treatment" class="form-control" value="<?php echo $row['treatment']; ?>" >
                                    </div>
                                    <div class="mb-2">
                                        <label>Fecha de Cirugía</label>
                                        <input type="date" name="surgery_date" class="form-control" value="<?php echo $row['surgery_date']; ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label>Fecha de Alta</label>
                                        <input type="date" name="discharge_date" class="form-control" value="<?php echo $row['discharge_date']; ?>" >
                                    </div>
                                    <button type="submit" class="btn btn-success">Guardar</button>
                                    <button type="button" class="btn btn-secondary" onclick="toggleEdit(<?php echo $row['dni']; ?>)">Cancelar</button>
                                </div>
                            </form>
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