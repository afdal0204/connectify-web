<?php
include '../config.php';

header('Content-Type: application/json; charset=utf-8');
ob_clean();

class ErrorController
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    public function handle($method)
    {
        switch ($method) {
            case 'GET':
                if (isset($_GET['type']) && $_GET['type'] === 'total') {
                    $this->getTotalErrorCode();
                } else {
                    $this->getAllErrors();
                }
                break;
            case 'POST':
                $this->addErrorCode();
                break;
            case 'DELETE':
                $this->deleteErrorCode();
                break;
            default:
                http_response_code(405);
                echo json_encode(["status" => "error", "message" => "Method not allowed"]);
                break;
                exit();
        }
    }

    private function getAllErrors()
    {
        $result = $this->conn->query("SELECT e.id AS error_id, e.error_code, e.symptom, 
                                        e.user_id, u.id AS user_id,
                                        u.name, u.work_id
            FROM error_code e
            LEFT JOIN users u ON e.user_id = u.id
            ORDER BY e.error_code ASC");
        $errors = [];

        while ($row = $result->fetch_assoc()) {
            $errors[] = $row;
        }
        echo json_encode([
            "success" => true,
            "data" => $errors
        ]);
        exit();
    }

    private function getTotalErrorCode()
    {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM error_code");
        $row = $result->fetch_assoc();

        echo json_encode([
            "success" => true,
            "total" => (int)$row['total']
        ]);
        exit();
    }

    private function addErrorCode()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $error_code = strtoupper(trim($data['error_code'] ?? ""));
        $symptom = strtoupper(trim($data['symptom'] ?? ""));
        $user_id = (int)$data['user_id'] ?? "";

        if (!$error_code || !$symptom) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required"]);
            exit();
        }

        $checkStmt = $this->conn->prepare("SELECT id FROM error_code WHERE error_code = ?");
        $checkStmt->bind_param('s', $error_code);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            http_response_code(409); // 409 Conflict
            echo json_encode(["success" => false, "message" => "Error Code already exist, please use from available option"]);
            exit();
        }


        $addError = $this->conn->prepare("INSERT INTO error_code (error_code, symptom, user_id) VALUES (?,?,?)");
        $addError->bind_param('ssi', $error_code, $symptom, $user_id,);

        if ($addError->execute()) {
            echo json_encode(["success" => true,"message" => "Error Code added successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to add Error Code", "error" => $addError->error]);
        }
    }

    public function deleteErrorCode()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        // $data = json_decode(file_get_contents("php://input"), true);
        // file_put_contents("debug_decoded.txt", print_r($data, true));
        
        $id = $data['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(["message" => "Error Code ID is required"]);
            exit();
        }

        $checkStmt = $this->conn->prepare("SELECT id FROM error_code WHERE id = ?");
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Error Code id not found"]);
            exit();
        }

        $deleteErrorCode = $this->conn->prepare("DELETE FROM error_code WHERE id = ?");
        $deleteErrorCode->bind_param('i', $id);

        if ($deleteErrorCode->execute()) {
            echo json_encode(["success"=>true, "message" => "Error code deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to delete error code", "error" => $deleteErrorCode->error]);
        }
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new ErrorController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}
