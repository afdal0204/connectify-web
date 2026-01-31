<?php
include '../config.php';

// === Set agar respon selalu JSON ===
header('Content-Type: application/json; charset=utf-8');
ob_clean();

class ServerController
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    public function handle($method)
    {
        switch (strtoupper($method)) {
            // case 'GET':
            //     $this->getAllServers();
            //     break;
            case 'GET':
                if (isset($_GET['type']) && $_GET['type'] === 'total') {
                    $this->getTotalServer();
                } else {
                    $this->getAllServers();
                }
                break;
            case 'POST':
                $this->addServer();
                break;
            case 'PUT':
                $this->editServer();
                break;
            case 'DELETE':
                $this->deleteServer();
                break;
            default:
                http_response_code(405);
                echo json_encode(["status" => "error", "message" => "Method not allowed"]);
                exit();
        }
    }

    private function getAllServers()
    {
        $result = $this->conn->query("SELECT s.id, s.server_ip, s.asset_number, 
                                        s.location_id, loc.location_name, s.remark
                                        FROM servers s
                                        LEFT JOIN server_location loc ON s.location_id = loc.id
                                        ORDER BY s.asset_number ASC");
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode([
            "success" => true,
            "data" => $users
        ]);
        exit();
    }
    private function getTotalServer()
    {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM users");
        $row = $result->fetch_assoc();

        echo json_encode([
            "success" => true,
            "total" => (int)$row['total']
        ]);
        exit();
    }


    private function addServer()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $server_ip = strtoupper(trim($data['server_ip'] ?? ""));
        $asset_number = strtoupper(trim($data['asset_number'] ?? ""));
        $location_id = (int)$data['location_id'] ?? "";
        $remark = $data['remark'] ?? "";

        if (!$server_ip || !$location_id) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required"]);
            exit();
        }

        $checkStmt = $this->conn->prepare("SELECT id FROM servers WHERE server_ip = ?");
        $checkStmt->bind_param('s', $server_ip);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            http_response_code(409); // 409 Conflict
            echo json_encode(["success" => false, "message" => "Server already exist"]);
            exit();
        }


        $addServer = $this->conn->prepare("INSERT INTO servers (server_ip, asset_number, location_id, remark) VALUES (?,?,?,?)");
        $addServer->bind_param('ssis', $server_ip, $asset_number, $location_id, $remark);

        if ($addServer->execute()) {
            echo json_encode(["success" => true,"message" => "Server added successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to add Server", "error" => $addServer->error]);
        }
    }

    private function editServer()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $id = isset($data['id']) ? intval($data['id']) : 0;

        $server_ip = strtoupper(trim($data['server_ip'] ?? ""));
        $asset_number = strtoupper(trim($data['asset_number'] ?? ""));
        $location_id = (int)$data['location_id'] ?? "";
        $remark = $data['remark'] ?? "";

        if (!$id    ) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required"]);
            exit();
        }

        $checkStmt = $this->conn->prepare("SELECT id FROM servers WHERE id = ?");
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Server not found"
            ]);
            exit();
        }

        $row = $result->fetch_assoc();
        $id = $row['id'];

        $editServer = $this->conn->prepare("UPDATE servers SET server_ip = ?, 
                                            asset_number = ?, location_id = ?, remark = ? WHERE id = ?");

        $editServer->bind_param('ssisi',
                $server_ip,
                $asset_number,
                $location_id,
                $remark,
                $id
            );

        if ($editServer->execute()) {
            echo json_encode(["success" => true, "message" => "Server edited successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to edit server", "error" => $editServer->error]);
        }
    }

    private function deleteServer()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $id = $data['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(["message" => "ID is required"]);
            exit();
        }

        $checkStmt = $this->conn->prepare("SELECT id FROM servers WHERE id = ?");
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Server not found"]);
            exit();
        }

        $deleteErrorCode = $this->conn->prepare("DELETE FROM servers WHERE id = ?");
        $deleteErrorCode->bind_param('i', $id);

        if ($deleteErrorCode->execute()) {
            echo json_encode(["success"=>true, "message" => "Server deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to delete server", "error" => $deleteErrorCode->error]);
        }
    }
}

// === Auto-handle access file directly ===
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new ServerController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}
