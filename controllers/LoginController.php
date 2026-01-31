<?php
include '../config.php';

// === Set agar respon selalu JSON ===
header('Content-Type: application/json; charset=utf-8');
ob_clean();

class LoginController {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function handle($method) {
        switch ($method) {
            case 'POST':
                $this->loginUser();
                break;
            default:
                http_response_code(405);
                echo json_encode([
                    "status" => "error", 
                    "message" => "Method not allowed"
                ]);
                exit();
        }
    }

    private function loginUser(){
        $data = json_decode(file_get_contents("php://input"), true);

        $work_id = trim($data['work_id'] ?? '');
        $password = $data['password'] ?? '';

        if (!$work_id || !$password) {
            http_response_code(400);
            echo json_encode([
                "status" => "error", 
                "message" => "Work ID and password are required"
            ]);
            exit;
        }

        // $stmt = $this->conn->prepare("SELECT * FROM users WHERE work_id = ?");
        $stmt = $this->conn->prepare("SELECT u.id, u.name, u.work_id, u.department_id,
                                                d.department_name AS department,
                                                u.role_id,
                                                ur.role_name AS role,
                                                u.password
                                            FROM users u
                                            LEFT JOIN department d ON u.department_id = d.id
                                            LEFT JOIN user_role ur ON u.role_id = ur.id
                                            WHERE u.work_id = ?
                                        ");

        $stmt->bind_param("s", $work_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {              

                $_SESSION['work_id'] = $user['work_id']; // trigger unauthorized

                unset($user['password']);
                echo json_encode([
                    "status" => "success",
                    "message" => "Login successfully", 
                    "user" => $user
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    "status" => "error", 
                    "message" => "Incorrect password"
                ]);
            }
        } else {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "User unregistered!"
            ]);
            exit();
        }
    }
}
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new LoginController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}