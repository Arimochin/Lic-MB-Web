<?php
// Configuración de conexión
$host = "localhost";
$port = "5432";
$dbname = "lic-MB-DB";
$user = "patient";
$password = "patient";

// Crear conexión
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Verificar conexión
if (!$conn) {
    die("Conexión fallida: " . pg_last_error());
} else {
    echo "Conexión exitosa";
}
