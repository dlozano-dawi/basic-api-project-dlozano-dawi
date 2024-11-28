<?php
header("Content-Type: application/json; charset=UTF-8");

include_once 'database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $user = $_GET['user'];
        $sql = "SELECT token FROM tokens WHERE user = :user";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user', $user);
        $stmt->execute();
        $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($tokens);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $sql = "INSERT INTO tokens (user, token) VALUES (:user, :token)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user', $data['user']);
        $token = bin2hex(random_bytes(16));
        $stmt->bindParam(':token', $token);

        try {
            if($stmt->execute()) {
                $sql = "SELECT token FROM tokens WHERE user = :user";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':user', $data['user']);
                $stmt->execute();
                $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo $tokens[0];
                echo json_encode(['mensaje' => 'Se ha creado un token para este usuario: $tokens']);
            } else {
                echo json_encode(['mensaje' => 'Error al crear el usuario']);
            }
        } catch (Throwable $th) {
            echo json_encode(['mensaje' => 'Este usuario ya tiene un token: ']);
        }
    
        break;
}        

function generarApiKey() {
    return bin2hex(random_bytes(16)); // Genera una API key de 32 caracteres
}
// 9a1c2e3f4g5h6j7k8l9m0n1o2p3q4r5s