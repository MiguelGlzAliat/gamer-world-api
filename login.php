<?php
header("Content-Type: application/json");
require 'vendor/autoload.php';
use \Firebase\JWT\JWT;

$conexion = include('conection.php');

if ($conexion['status']) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        $conn = $conexion['connection'];

        $user = $data['email'];
        $pass = password_hash($data['password'], PASSWORD_BCRYPT);

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($data['password'], $row['password'])) {
                unset($row['password']);
                unset($row['CreatedAt']);
                unset($row['UpdatedAt']);
                $now = time();
                $payload = array(
                    "iat" => $now,
                    "exp" => $now + 1800,
                    "data" => $row
                );
                $jwt = JWT::encode($payload, 'T3mp0r4l$3cr3tK3y', 'HS256');
                http_response_code(200);
                echo json_encode(["status" => "success", "token" => $jwt]);
            } else {
                http_response_code(401);
                echo json_encode(["status" => "error", "message" => "Contraseña incorrecta"]);
            }
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "El usuario no existe"]);
        }

        $stmt->close();
        $conn->close();
        die();
    } else {
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
        die();
    }
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Conexión a la base de datos fallida"]);
    die();
}
?>