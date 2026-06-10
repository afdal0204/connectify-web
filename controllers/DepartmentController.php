<?php
include '../config.php';

header('Content-Type: application/json; charset=utf-8');
ob_clean();

class DepartmentController
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
                    $this->getAllDepartment();
                break;
            case 'POST':
                $this->addDepartment();
                break;
            case 'DELETE':
                $this->deleteDepartment();
                break;
            default:
                http_response_code(405);
                echo json_encode(["status" => "error", "message" => "Method not allowed"]);
                break;
                exit();
        }
    }

    private function getAllDepartment()
    {
        $result = $this->conn->query("SELECT d.id, d.department_name, d.description, d.remark
            FROM department d
            ORDER BY d.department_name ASC");
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

    private function addDepartment()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $department_name = strtoupper(trim($data['department_name'] ?? ""));
        $description = strtoupper(trim($data['description'] ?? ""));
        $remark = strtoupper(trim($data['remark'] ?? ""));

        if (!$department_name || !$description) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required"]);
            exit();
        }

        $checkStmt = $this->conn->prepare("SELECT id FROM department WHERE department_name = ?");
        $checkStmt->bind_param('s', $department_name);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            http_response_code(409); // 409 Conflict
            echo json_encode(["success" => false, "message" => "Department name already exist"]);
            exit();
        }


        $addDepartment = $this->conn->prepare("INSERT INTO department (department_name, description, remark) VALUES (?,?,?)");
        $addDepartment->bind_param('sss', $department_name, $description, $remark,);

        if ($addDepartment->execute()) {
            echo json_encode(["success" => true,"message" => "Department added successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to add Department", "error" => $addDepartment->error]);
        }
    }

    public function deleteDepartment()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(["message" => "Department ID is required"]);
            exit();
        }

        $checkStmt = $this->conn->prepare("SELECT id FROM department WHERE id = ?");
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Department id not found"]);
            exit();
        }

        $deleteDepartment = $this->conn->prepare("DELETE FROM department WHERE id = ?");
        $deleteDepartment->bind_param('i', $id);

        if ($deleteDepartment->execute()) {
            echo json_encode(["success"=>true, "message" => "Department deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to delete department", "error" => $deleteDepartment->error]);
        }
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new DepartmentController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}
