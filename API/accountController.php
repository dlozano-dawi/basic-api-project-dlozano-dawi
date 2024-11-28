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
        $sql = "SELECT * FROM account";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $account = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($account);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $sql = "INSERT INTO account (money, user_id) VALUES (:money, :user_id)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':money', $data['money']);
        $stmt->bindParam(':user_id', $data['user_id']);

        if($stmt->execute()) {
            echo json_encode(['mensaje' => 'Cuenta bancaria creada']);
        } else {
            echo json_encode(['mensaje' => 'Error al crear la cuenta bancaria']);
        }

        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $sql = "UPDATE account SET money = money + :money WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':money', $data['money']);
        if($stmt->execute()) {
            echo json_encode(['mensaje' => 'Operacion realizada correctamente']);
        } else {
            echo json_encode(['mensaje' => 'Error al realizar la operacion']);
        }
        break;
    case 'DELETE':
        $id = $_GET['id'];
        $sql = "DELETE FROM account WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        if($stmt->execute()) {
            echo json_encode(['mensaje' => 'Cuenta eliminada']);
        } else {
            echo json_encode(['mensaje' => 'Error al eliminar la cuenta']);
        }
        break;
  default:
    echo json_encode(['mensaje' => 'Método no soportado']);
    break;
}
?>