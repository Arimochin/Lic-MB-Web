<?php
session_start();

if (isset($_POST['login'])) {
    // Conectar a la base de datos
    $host = "localhost";
    $port = "5432";
    $dbname = "lic-MB-DB";
    $username = filter_var($_POST['username'], FILTER_UNSAFE_RAW);
    $password = filter_var($_POST['password'], FILTER_UNSAFE_RAW);

    $conn = pg_connect("host=$host port=$port dbname=$dbname user=$username password=$password");

    // Verificar la conexión
    if (!$conn) {
        die("Error de conexión: " . pg_last_error());
    }
    


    // Preparar la consulta SQL
    $query = "SELECT id, password FROM users WHERE username = $1";
    $result = pg_query_params($conn, $query, [$username]);

    // Verificar si el usuario existe
    if ($result && pg_num_rows($result) > 0) {
        $query = "SELECT 1 FROM users WHERE username = $1 and password = crypt($2, password); ";
        $result = pg_query_params($conn, $query, [$username, $password]);
        $row = pg_fetch_result($result, 0, 0); // Obtener el valor de la primera fila y primera columna
        if ($row == 1) {
            // Iniciar sesión
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            header("Location: ../dashboard.php");
            exit;
        } else {
            echo "Contraseña incorrecta!";
        }
    } else {
        echo "¡Usuario no encontrado!";
    }
}