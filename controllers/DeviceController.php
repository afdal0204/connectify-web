<?php
include '../config.php';

header('Content-Type: application/json; charset=utf-8');
ob_clean();

class DeviceController {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function handle($method){
        switch ($method){
            case 'GET':
                $this->getDevicesByStation();
                break;
            case 'POST':
                $this->addDevicesBulk();
                break;
            default:
                http_response_code(405);
                echo json_encode(["status" => "error", "message" => "Method not allowed"]);
                break;
                exit();
        }
    }

    private function getDevicesByStation() {
        $station_id = $_GET['station_id'] ?? null;
        if (!$station_id) {
            http_response_code(400);
            echo json_encode(["message" => "station_id parameter is required"]);
            exit();
        }

        $stmt = $this->conn->prepare("SELECT id, station_id, device_name 
                                        FROM devices WHERE station_id = ?
                                        ORDER BY device_name ASC");
        $stmt->bind_param("i", $station_id);
        $stmt->execute();
        $res = $stmt->get_result();

        $devs = [];
        while ($row = $res->fetch_assoc()) {
            $devs[] = $row;
        }
        echo json_encode($devs);

    }

    private function addDevicesBulk()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);

        $station_id = isset($data['station_id']) ? trim($data['station_id']) : null;
        $device_name = isset($data['device_name']) ? $data['device_name'] : null;

        if (!$station_id || !is_array($device_name) || count($device_name) === 0) {
            http_response_code(400);
            echo json_encode(["message" => "Station Name and Device ID array are required"]);
            exit();
        }

        $success = [];
        $errors = [];

        foreach ($device_name as $name) {
            $device_name = strtoupper(trim($name));
            if ($device_name === '') {
                $errors[] = ["name" => $name, "error" => "Empty name"];
                continue;
            }

            $checkStmt = $this->conn->prepare("
                SELECT id FROM devices 
                WHERE station_id = ? AND device_name = ?
                LIMIT 1
            ");
            $checkStmt->bind_param('is', $station_id, $device_name);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            if ($result && $result->num_rows > 0) {
                $errors[] = ["name" => $device_name, "error" => "Already exists"];
                continue;
            }

            $insertStmt = $this->conn->prepare("
                INSERT INTO devices (station_id, device_name) 
                VALUES (?, ?)
            ");
            $insertStmt->bind_param('is', $station_id, $device_name);
            if ($insertStmt->execute()) {
                $success[] = $device_name;
            } else {
                $errors[] = ["name" => $device_name, "error" => $insertStmt->error];
            }
        }

        http_response_code(200);
        echo json_encode([
            "message" => "Information",
            "success" => $success,
            "errors" => $errors
        ]);
    }
}


if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new DeviceController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}