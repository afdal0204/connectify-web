<?php
include '../config.php';

// === Set agar respon selalu JSON ===
header('Content-Type: application/json; charset=utf-8');
ob_clean();

class ModelController
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
                if (isset($_GET['id'])) {
                    $this->getModelById($_GET['id']);
                } else {
                    $this->getAllModels();
                }
                break;
            case 'POST':
                // $this->addModels();
                $action = $_GET['action'] ?? '';
                if($action === 'update') {
                    $this->editModels();
                } else {
                    $this->addModels();
                }
                break;
            case 'PUT':
                $this->editModels();
                break;
            case 'DELETE':
                $this->deleteModels();
                break;
            default:
                http_response_code(405);
                echo json_encode(["status" => "error", "message" => "Method not allowed"]);
                break;
                exit();
        }
    }

    private function getAllModels()
    {
        $models = [];

        $sql = "SELECT m.id, m.model_name, m.line_area,
                u_owner.name AS owner, m.owner_id,
                GROUP_CONCAT(u_member.name SEPARATOR ', ') AS members,
                GROUP_CONCAT(mm.member_id) AS member_ids
            FROM models m
            JOIN users u_owner ON m.owner_id = u_owner.id
            LEFT JOIN model_members mm ON m.id = mm.model_id
            LEFT JOIN users u_member ON mm.member_id = u_member.id
            GROUP BY m.id, m.model_name, m.line_area, u_owner.name
            ORDER BY m.model_name ASC, m.line_area ASC";

        $res = $this->conn->query($sql);
        while ($row = $res->fetch_assoc()) {
            $model = $row;

            $model_id = $row['id'];
            $stations = [];
            $stmtS = $this->conn->prepare("SELECT * FROM stations WHERE model_id = ?");
            $stmtS->bind_param('i', $model_id);
            $stmtS->execute();
            $resS = $stmtS->get_result();
            while ($rS = $resS->fetch_assoc()) {
                $station = $rS;

                $stationId = $rS['id'];
                $devices = [];
                $stmtD = $this->conn->prepare("SELECT * FROM devices WHERE station_id = ?");
                $stmtD->bind_param('i', $stationId);
                $stmtD->execute();
                $resD = $stmtD->get_result();
                while ($rD = $resD->fetch_assoc()) {
                    $devices[] = $rD;
                }
                $station['devices'] = $devices;

                $stations[] = $station;
            }

            $model['stations'] = $stations;
            $models[] = $model;
        }

        echo json_encode([
            "success" => true,
            "data" => $models
        ]);
        exit();
    }

    private function getModelById($id)
    {
        $stmt = $this->conn->prepare("SELECT 
            m.id, m.model_name, m.line_area,
            m.owner_id, u_owner.name AS owner,  
            GROUP_CONCAT(u_member.name SEPARATOR ', ') AS members,
            GROUP_CONCAT(mm.member_id) AS member_ids
            FROM models m
            JOIN users u_owner ON m.owner_id = u_owner.id
            LEFT JOIN model_members mm ON m.id = mm.model_id
            LEFT JOIN users u_member ON mm.member_id = u_member.id
            WHERE m.id = ?
            GROUP BY m.id");

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $model = $res->fetch_assoc();

        $stmtS = $this->conn->prepare("SELECT * FROM stations WHERE model_id = ?");
        $stmtS->bind_param('i', $id);
        $stmtS->execute();
        $resS = $stmtS->get_result();
        $stations = [];
        while ($rS = $resS->fetch_assoc()) {
            $stationId = $rS['id'];
            $stmtD = $this->conn->prepare("SELECT * FROM devices WHERE station_id = ?");
            $stmtD->bind_param('i', $stationId);
            $stmtD->execute();
            $resD = $stmtD->get_result();
            $rS['devices'] = $resD->fetch_all(MYSQLI_ASSOC);
            $stations[] = $rS;
        }
        $model['stations'] = $stations;

        echo json_encode(["success" => true, "data" => $model]);
        exit;
    }

    private function addModels()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $model_name = strtoupper(trim($data['model_name'] ?? ""));
        $line_area = strtoupper(trim($data['line_area'] ?? ""));
        $owner_id = intval($data['owner_id'] ?? 0);
        $members = $data['members'] ?? [];

        if (!$model_name || !$line_area || !$owner_id) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required"]);
            exit();
        }

        $checkOwner = $this->conn->prepare("SELECT id FROM users WHERE id = ?");
        $checkOwner->bind_param('i', $owner_id);
        $checkOwner->execute();
        if ($checkOwner->get_result()->num_rows === 0) {
            http_response_code(400);
            echo json_encode(["message" => "Owner ID does not exist"]);
            exit();
        }

        $checkStmt = $this->conn->prepare("SELECT id FROM models WHERE model_name = ?");
        $checkStmt->bind_param('s', $model_name);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            http_response_code(409);
            echo json_encode(["message" => "Model Name already exists"]);
            exit();
        }

        $addModel = $this->conn->prepare("INSERT INTO models (model_name, line_area, owner_id) VALUES (?,?,?)");
        $addModel->bind_param('ssi', $model_name, $line_area, $owner_id);

        if ($addModel->execute()) {
            $model_id = $addModel->insert_id;

            if (!empty($members)) {
                $stmtMember = $this->conn->prepare("INSERT INTO model_members (model_id, member_id) VALUES (?, ?)");
                foreach ($members as $member_id) {
                    $mid = intval($member_id);
                    $stmtMember->bind_param('ii', $model_id, $mid);
                    $stmtMember->execute();
                }
            }

            echo json_encode(["success" => true, "message" => "New model added successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to add model", "error" => $addModel->error]);
        }
    }

    private function editModels()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $id = intval($data['id'] ?? 0); // model_id
        $line_area = strtoupper(trim($data['line_area'] ?? ""));
        // $owner_id = $data['owner_id'] ?? [];
        $owner_id = strtoupper(trim($data['owner_id'] ?? ''));
        $members = $data['members'] ?? [];
        $stations = $data['stations'] ?? [];
        $devicesData = $data['devices'] ?? [];

        if (!$id || !$line_area) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "All fields are required"]);
            exit();
        }

        // --- Update line_area  ---
        $stmt = $this->conn->prepare("UPDATE models SET line_area = ?, owner_id = ? WHERE id = ?");
        $stmt->bind_param('sii', $line_area, $owner_id, $id);
        $stmt->execute();

        // --- Update Members ---
        if (!empty($members)) {
            $stmtCheckMember = $this->conn->prepare("SELECT id FROM model_members WHERE model_id = ? AND member_id = ?");
            $stmtInsertMember = $this->conn->prepare("INSERT INTO model_members (model_id, member_id) VALUES (?, ?)");

            foreach ($members as $member_id) {
                $mid = intval($member_id);
                $stmtCheckMember->bind_param('ii', $id, $mid);
                $stmtCheckMember->execute();
                $res = $stmtCheckMember->get_result();

                if ($res->num_rows === 0) {
                    $stmtInsertMember->bind_param('ii', $id, $mid);
                    $stmtInsertMember->execute();
                }
            }
        }

        // --- Add/Update Stations ---
        $stationIds = []; 
        if (!empty($stations)) {
            $stmtCheckStation = $this->conn->prepare("SELECT id FROM stations WHERE model_id = ? AND station_name = ?");
            $stmtInsertStation = $this->conn->prepare("INSERT INTO stations (model_id, station_name) VALUES (?, ?)");
            $stmtSelectStationId = $this->conn->prepare("SELECT id FROM stations WHERE model_id = ? AND station_name = ?");

            foreach ($stations as $station_name) {
                // $station = trim($station_name);
                $station = strtoupper(trim($station_name));
                if ($station === '') continue;

                $stmtCheckStation->bind_param('is', $id, $station);
                $stmtCheckStation->execute();
                $res = $stmtCheckStation->get_result();

                if ($res->num_rows === 0) {
                    $stmtInsertStation->bind_param('is', $id, $station);
                    $stmtInsertStation->execute();
                }

                $stmtSelectStationId->bind_param('is', $id, $station);
                $stmtSelectStationId->execute();
                $resId = $stmtSelectStationId->get_result();
                if ($resId->num_rows > 0) {
                    $row = $resId->fetch_assoc();
                    $stationIds[$station] = intval($row['id']);
                }
            }
        }

        // --- Add/Update Devices ---
        // Devices sekarang pakai langsung station_id & device_name
        if (!empty($devicesData)) {
            $stmtCheckDevice = $this->conn->prepare("SELECT id FROM devices WHERE station_id = ? AND device_name = ?");
            $stmtInsertDevice = $this->conn->prepare("INSERT INTO devices (station_id, device_name) VALUES (?, ?)");

            foreach ($devicesData as $device) {
                $stationId = intval($device['station_id'] ?? 0);
                $deviceName = strtoupper(trim($device['device_name'] ?? ''));
                // $deviceName = trim($device['device_name'] ?? '');

                if (!$stationId || $deviceName === '') continue;

                $stmtCheckDevice->bind_param('is', $stationId, $deviceName);
                $stmtCheckDevice->execute();
                $res = $stmtCheckDevice->get_result();

                if ($res->num_rows === 0) {
                    $stmtInsertDevice->bind_param('is', $stationId, $deviceName);
                    $stmtInsertDevice->execute();
                }
            }
        }

        echo json_encode(["success" => true, "message" => "Model updated successfully"]);
    }

    private function deleteModels()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        $model_name = strtoupper(trim($data['model_name'] ?? ''));
        if (!$model_name) {
            http_response_code(400);
            echo json_encode(["message" => "Model Name is required"]);
            exit();
        }

        $this->conn->begin_transaction();

        try {
            $stmt0 = $this->conn->prepare("SELECT id FROM models WHERE UPPER(model_name) = ?");
            $stmt0->bind_param("s", $model_name);
            $stmt0->execute();
            $res0 = $stmt0->get_result();
            if ($res0->num_rows === 0) {
                throw new Exception("Model not found", 404);
            }
            $row0 = $res0->fetch_assoc();
            $model_id = (int)$row0['id'];

            // delete device
            $stmtD = $this->conn->prepare("
                DELETE d 
                FROM devices d
                INNER JOIN stations s ON d.station_id = s.id
                WHERE s.model_id = ?
            ");
            $stmtD->bind_param("i", $model_id);
            $stmtD->execute();

            // Delete stations from it model
            $stmtS = $this->conn->prepare("DELETE FROM stations WHERE model_id = ?");
            $stmtS->bind_param("i", $model_id);
            $stmtS->execute();

            // Delete model
            $stmtM = $this->conn->prepare("DELETE FROM models WHERE id = ?");
            $stmtM->bind_param("i", $model_id);
            $stmtM->execute();

            if ($stmtM->affected_rows > 0) {
                $this->conn->commit();
                echo json_encode(["message" => "Model deleted successfully"]);
            } else {
                throw new Exception("Failed to delete model", 500);
            }
        } catch (Exception $ex) {
            $this->conn->rollback();
            $code = $ex->getCode();
            if ($code === 404) {
                http_response_code(404);
                echo json_encode(["message" => $ex->getMessage()]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to delete model", "error" => $ex->getMessage()]);
            }
        }
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new ModelController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}
