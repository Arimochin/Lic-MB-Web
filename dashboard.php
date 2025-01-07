<?php
require_once 'includes/dbh.inc.terap.php';
$query = "SELECT * FROM DATOS_PERSONALES";
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
        <?php
        while($row = pg_fetch_assoc($result)){
        ?>
        <div class="card">
            <p><?php echo $row['dni']; ?></p>
        </div>
        <?php } ?>
    </body>
</html>