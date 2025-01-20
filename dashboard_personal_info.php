<?php
require_once 'includes/dbh.inc.terap.php';
$query = "SELECT * FROM DATOS_PERSONALES";
// Inicializar variables
$search = '';
// Procesar búsqueda si se envía el formulario
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $query .= " WHERE LOWER(firstname) LIKE LOWER('%$search%') 
                OR LOWER(secondname) LIKE LOWER('%$search%') 
                OR CAST(dni AS TEXT) LIKE '%$search%'";
}
$result = pg_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Lic MB</title> <!-- nombre temporal -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <div class="container py-4">
        <!-- Botón derecho -->
        <div class="d-flex justify-content-end mb-3">
            <a href="dashboard_medical_info.php" class="heading">Ver Fichas Médicas ></a>
        </div>

        <!-- Barra de búsqueda -->
        <form action="" method="get" class="d-flex mb-4">
            <input 
                type="text" 
                name="search" 
                class="form-control me-2 search-input" 
                placeholder="Buscar por nombre, apellido o DNI..." 
                value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="search-btn">Buscar</button>
        </form>

        <!-- Resultados -->
        <div class="row g-4">
            <?php while ($row = pg_fetch_assoc($result)) { ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm">
                        <div class="info-card card-body">
                            <h5 class="person-name"><?php echo $row['firstname'] . ' ' . $row['secondname']; ?></h5>
                            <p class="card-text"><strong>DNI:</strong> <?php echo $row['dni']; ?></p>
                            <p class="card-text"><strong>Fecha de Nacimiento:</strong> <?php echo $row['date_of_birth']; ?></p>
                            <p class="card-text"><strong>Dirección:</strong> <?php echo $row['adress']; ?></p>
                            <p class="card-text"><strong>Teléfono:</strong> <?php echo $row['phone']; ?></p>
                            <p class="card-text"><strong>Obra Social:</strong> <?php echo $row['os']; ?></p>
                            <p class="card-text"><strong>Disponibilidad Horaria:</strong> <?php echo $row['schedule']; ?></p>

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

                            <!-- Botón para ficha médica -->
                            <form action="includes/move_to_ficha.php" method="post" class="mt-3">
                                <input type="hidden" name="dni" value="<?php echo $row['dni']; ?>">
                                <button type="submit" name="move_to_ficha" class="btn-sm move-btn">Pasar a Ficha Médica</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>