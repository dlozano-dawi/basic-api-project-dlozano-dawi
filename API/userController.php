<?php
header("Content-Type: application/json; charset=UTF-8");

include_once 'database.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

$headers = apache_request_headers();

if (isset($headers['Authorization'])) {
    $apiKey = str_replace('Bearer ', '', $headers['Authorization']);
} else {
    $apiKey = '';
}

$stmt = $db->prepare("SELECT * FROM tokens WHERE token = :api_key");
$stmt->bindParam(':api_key', $apiKey);
$stmt->execute();
$usuario = $stmt->fetch();

if (!$usuario) {
    echo json_encode("Invalid Api Key");
    exit;
}

switch ($method) {
    case 'GET':
        $sql = "SELECT * FROM user";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($user);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $sql = "INSERT INTO user (name, lastname, dni) VALUES (:name, :lastname, :dni)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':lastname', $data['lastname']);
        $stmt->bindParam(':dni', $data['dni']);

        try {
            if($stmt->execute()) {
                echo json_encode(['mensaje' => 'Usuario creada']);
            } else {
                echo json_encode(['mensaje' => 'Error al crear el usuario']);
            }
        } catch (Throwable $th) {
            echo json_encode(['mensaje' => 'El dni ya está asociado a otro usuario']);
        }

        break;
    default:
        echo json_encode(['mensaje' => 'Método no soportado']);
        break;
}
?>