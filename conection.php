<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gamer_world";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    return [
        "status" => false,
        "message" => "Connection failed: " . $conn->connect_error
    ];
} else {
    return [
        "status" => true,
        "connection" => $conn
    ];
}
?>