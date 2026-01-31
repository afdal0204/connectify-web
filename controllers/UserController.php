<?php
include '../config.php';

// === Set agar respon selalu JSON ===
header('Content-Type: application/json; charset=utf-8');
ob_clean();

class UserController
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    public function handle($method)
    {
        switch (strtoupper($method)) {
            case 'GET':
                if (isset($_GET['type']) && $_GET['type'] === 'total') {
                    $this->getTotalUsers();
                } else {
                    $this->getAllUsers();
                }
                break;
            case 'POST':
                $this->addUser();
                break;
            case 'PUT':
                $this->editUser();
                break;
            case 'DELETE':
                $this->deleteUser();
                break;
            default:
                http_response_code(405);
                echo json_encode(["status" => "error", "message" => "Method not allowed"]);
                exit();
        }
    }

    // private function getAllUsers()
    // {
    //     $result = $this->conn->query("SELECT u.id, u.name, u.department_id, u.work_id, u.role, d.department_name AS department,
    //                                         u.role_id, ur.role_name, u.last_activity,
    //                                         IF(
    //                                             u.is_online = 1 AND TIMESTAMPDIFF(MINUTE, u.last_activity, NOW()) < 2,
    //                                             1,
    //                                             0
    //                                         ) AS is_online
    //                                 FROM users u
    //                                 LEFT JOIN department d ON u.department_id = d.id
    //                                 LEFT JOIN user_role ur ON u.role_id = ur.id
    //                                 ORDER BY u.name ASC");
    //     $users = [];
    //     while ($row = $result->fetch_assoc()) {
    //         $users[] = $row;
    //     }
    //     echo json_encode([
    //         "success" => true,
    //         "data" => $users
    //     ]);
    //     exit();
    // }
    private function getAllUsers()
    {
        $sql = "SELECT u.id, u.name, u.department_id, u.work_id,
                d.department_name AS department, u.role_id, ur.role_name, u.last_activity,

                IF(
                    u.is_online = 1 AND TIMESTAMPDIFF(MINUTE, u.last_activity, NOW()) < 2, 1, 0
                ) AS is_online,

                (
                    SELECT GROUP_CONCAT(model_name SEPARATOR ', ')
                    FROM models 
                    WHERE owner_id = u.id
                ) AS owned_models,

                (
                    SELECT GROUP_CONCAT(m.model_name SEPARATOR ', ')
                    FROM model_members mm
                    JOIN models m ON m.id = mm.model_id
                    WHERE mm.member_id = u.id
                ) AS member_models

            FROM users u
            LEFT JOIN department d ON u.department_id = d.id
            LEFT JOIN user_role ur ON u.role_id = ur.id
            ORDER BY u.name ASC";

        $result = $this->conn->query($sql);

        $users = [];
        while ($row = $result->fetch_assoc()) {

            // gabungkan owner + member
            $models = [];

            if (!empty($row['owned_models'])) {
                $models[] = $row['owned_models'];
            }

            if (!empty($row['member_models'])) {
                $models[] = $row['member_models'];
            }

            // hasil akhir: satu list model
            $row['all_models'] = implode(", ", $models);

            $users[] = $row;
        }

        echo json_encode([
            "success" => true,
            "data" => $users
        ]);
        exit();
    }

    private function getTotalUsers()
    {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM users");
        $row = $result->fetch_assoc();

        echo json_encode([
            "success" => true,
            "total" => (int)$row['total']
        ]);
        exit();
    }

    private function addUser()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $name = strtoupper(trim($data['name'] ?? null));
        $work_id = strtoupper(trim($data['work_id'] ?? null));
        $password = $data['password'] ?? null;
        $department_id = strtoupper(trim($data['department_id'] ?? null));
        $role_id = strtoupper(trim($data['role_id'] ?? null));

        if (!$name || !$work_id || !$password || !$department_id || !$role_id) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required"]);
            exit();
        }

        $checkStmt = $this->conn->prepare("SELECT id FROM users WHERE work_id = ?");
        $checkStmt->bind_param('s', $work_id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            http_response_code(409); // Conflict
            echo json_encode(["message" => "Work ID already exists"]);
            exit();
        }

        $hashPassword = password_hash($password, PASSWORD_DEFAULT);

        $addUser = $this->conn->prepare("INSERT INTO users (name, work_id, password, department_id, role_id) VALUES (?,?,?,?,?)");
        $addUser->bind_param('sssss', $name, $work_id, $hashPassword, $department_id, $role_id);

        if ($addUser->execute()) {
            echo json_encode(["success" => true, "message" => "User added successfully"]);
            exit();
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to add user", "error" => $addUser->error]);
            exit();
        }
    }

    private function editUser()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $name = strtoupper(trim($data['name'] ?? ''));
        $work_id = strtoupper(trim($data['work_id'] ?? ''));
        $password = $data['password'] ?? '';
        $department_id = strtoupper(trim($data['department_id'] ?? ''));
        $role_id = strtoupper(trim($data['role_id'] ?? ''));

        if (!$name || !$work_id || !$department_id || !$role_id || !$password ) {
            $missing = [];
            if (!$name) $missing[] = 'name';
            if (!$work_id) $missing[] = 'work_id';
            if (!$department_id) $missing[] = 'department_id';
            if (!$role_id) $missing[] = 'role_id';
            if (!$password) $missing[] = 'password';
            http_response_code(400);
            echo json_encode(["message" => "All fields are required", 'missing_field' => $missing]);
            exit();
        }

        $name = strtoupper(trim($data['name'] ?? null));
        $work_id = strtoupper(trim($data['work_id'] ?? null));
        $password = $data['password'] ?? null;
        $department_id = strtoupper(trim($data['department_id'] ?? null));
        $role_id = strtoupper(trim($data['role_id'] ?? null));

        if (!$name || !$work_id || !$password || !$department_id || !$role_id) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required"]);
            exit();
        }

        $checkStmt = $this->conn->prepare("SELECT work_id FROM users WHERE work_id = ?");
        $checkStmt->bind_param('s', $work_id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["message" => "User not found"]);
            exit();
        }

        if ($password) {
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateUser = $this->conn->prepare("UPDATE users SET name = ?, password = ?, department_id = ?, role_id = ? WHERE work_id = ?");
            $updateUser->bind_param('sssss', $name, $hashPassword, $department_id, $role_id, $work_id);
        } else {
            $updateUser = $this->conn->prepare("UPDATE users SET name = ?, department_id = ?, role_id = ? WHERE work_id = ?");
            $updateUser->bind_param('ssss', $name, $department_id, $role_id, $work_id);
        }

        if ($updateUser->execute()) {
            echo json_encode(["success" => true, "message" => "User updated successfully"]);
            exit();
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to update user", "error" => $updateUser->error]);
            exit();
        }
    }

    private function deleteUser()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $work_id = $data['work_id'] ?? '';

        if (!$work_id) {
            http_response_code(400);
            echo json_encode(["message" => "Work ID is required"]);
            exit();
        }

        $checkStmt = $this->conn->prepare("SELECT id, work_id FROM users WHERE work_id = ?");
        $checkStmt->bind_param('s', $work_id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["message" => "User not found"]);
            exit();
        }

        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        // Hapus relasi di tabel model_members
        $deleteRelations = $this->conn->prepare("DELETE FROM model_members WHERE member_id = ?");
        $deleteRelations->bind_param('i', $user_id);
        $deleteRelations->execute();
        
        $deleteUser = $this->conn->prepare("DELETE FROM users WHERE work_id = ?");
        $deleteUser->bind_param('s', $work_id);

        if ($deleteUser->execute()) {
            echo json_encode(["success" => true, "message" => "User deleted successfully"]);
            exit();
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to delete user", "error" => $deleteUser->error]);
            exit();
        }
    }
}

// === Auto-handle access file directly ===
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new UserController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}
