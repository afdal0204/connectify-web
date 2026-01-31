<?php
include '../config.php';

// === Set agar respon selalu JSON ===
header('Content-Type: application/json; charset=utf-8');
ob_clean();

class StationController {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function handle($method){
        switch ($method){
            case 'GET':
                $this->getStationsByModel();
                break;
            case 'POST':
                $this->addStationsBulk();
                break;
            default:
                http_response_code(405);
                echo json_encode(["status" => "error", "message" => "Method not allowed"]);
                break;
                exit();
        }
    }
    private function getStationsByModel() {
        $model_id = $_GET['model_id'] ?? null;
        if (!$model_id) {
            http_response_code(400);
            echo json_encode(["message" => "model_id parameter is required"]);
            exit();
        }

        $stmt = $this->conn->prepare("SELECT id, model_id, station_name FROM stations 
                                        WHERE model_id = ? 
                                        ORDER BY station_name ASC");
        $stmt->bind_param("i", $model_id);
        $stmt->execute();
        $res = $stmt->get_result();

        $stations = [];
        while ($row = $res->fetch_assoc()) {
            $stations[] = $row;
        }
        echo json_encode($stations);       
    }

    private function addStationsBulk()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);

        $model_id = isset($data['model_id']) ? trim($data['model_id']) : null;
        $station_name = isset($data['station_name']) ? $data['station_name'] : null;

        if (!$model_id || !is_array($station_name) || count($station_name) === 0) {
            http_response_code(400);
            echo json_encode(["message" => "model_id and station_name array are required"]);
            exit();
        }

        $success = [];
        $errors = [];

        foreach ($station_name as $name) {
            $station_name = strtoupper(trim($name));
            if ($station_name === '') {
                $errors[] = ["name" => $name, "error" => "Empty name"];
                continue;
            }

            $checkStmt = $this->conn->prepare("
                SELECT id FROM stations 
                WHERE model_id = ? AND station_name = ?
                LIMIT 1
            ");
            $checkStmt->bind_param('is', $model_id, $station_name);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            if ($result && $result->num_rows > 0) {
                $errors[] = ["name" => $station_name, "error" => "Already exists"];
                continue;
            }

            $insertStmt = $this->conn->prepare("
                INSERT INTO stations (model_id, station_name) 
                VALUES (?, ?)
            ");
            $insertStmt->bind_param('is', $model_id, $station_name);
            if ($insertStmt->execute()) {
                $success[] = $station_name;
            } else {
                $errors[] = ["name" => $station_name, "error" => $insertStmt->error];
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
    $controller = new StationController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}