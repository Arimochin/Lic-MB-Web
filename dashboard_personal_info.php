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
$result = pg_query($conn,$query);
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
    <body>
    <div class="container mt-4">
            <!-- Barra de búsqueda -->
            <form action="" method="get" class="d-flex mb-4">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control me-2" 
                    placeholder="Buscar por nombre, apellido o DNI..." 
                    value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </form>
        <?php

        
        while($row = pg_fetch_assoc($result)){
        ?>
        <div class="card">
            <p><?php echo $row['dni']; ?></p>
            <p><?php echo $row['firstname']; ?></p>
            <p><?php echo $row['secondname']; ?></p>
            <p><?php echo $row['date_of_birth']; ?></p>
            <p><?php echo $row['adress']; ?></p>
            <p><?php echo $row['phone']; ?></p>
            <p><?php echo $row['os']; ?></p>

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#derMedModal">Ver Derivación Médica</button>
            <div class="modal" id="derMedModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <img src="derMed-images/<?php echo htmlspecialchars($row['dev'], ENT_QUOTES, 'UTF-8'); ?>" >
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#autObSModal">Ver Autorización Obra Social</button>
            <div class="modal" id="autObSModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <img src="autObS-images/<?php echo htmlspecialchars($row['autor'], ENT_QUOTES, 'UTF-8'); ?>" >
                    </div>
                </div>
            </div>

            <!-- Botón para mover a ficha médica -->
            <form action="includes/move_to_ficha.php" method="post" class="mt-2">
                <input type="hidden" name="dni" value="<?php echo $row['dni']; ?>">
                <button type="submit" name="move_to_ficha" class="btn btn-success">Pasar a Ficha Médica</button>
            </form>
        </div>
        <?php } ?>
    </body>
</html>