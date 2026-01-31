<?php
include '../config.php';

// === Set agar respon selalu JSON ===
header('Content-Type: application/json; charset=utf-8');
ob_clean();

$method = $_SERVER['REQUEST_METHOD'];

class DownloadFilesController {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function handle($method) {
        switch ($method) {
            case 'GET':
                $this->downloadFile();
                break;
            // case 'POST':
            //     $this->downloadFile();
            //     break;
            default:
                http_response_code(405);
                echo json_encode(["status" => "error", "message" => "Method not allowed"]);
                break;
        }
    }

    private function downloadFile() {
        $input = null;
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $input = json_decode($raw, true);
        }
        if (!is_array($input)) {
            $input = [];
        }
        if (!isset($input['id'])) {
            if (isset($_GET['id'])) {
                $input['id'] = $_GET['id'];
            }
        }

        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "File id not provided"]);
            exit;
        }

        $id = intval($input['id']);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid id"]);
            exit;
        }

        $sql = "SELECT fileName, type, file_data FROM files WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            // error preparing
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Database prepare failed"]);
            exit;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result === false) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Database query failed"]);
            exit;
        }

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "File not found"]);
            exit;
        }

        $row = $result->fetch_assoc();
        $fileName = $row['fileName'];
        $mimeType = $row['type'];
        if (empty($mimeType)) {
            $mimeType = "application/octet-stream";
        }
        $fileData = $row['file_data'];

        if ($fileData === null) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "File data is empty"]);
            exit;
        }

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($fileData));

        // Output data
        echo $fileData;
        exit;
    }
}
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new DownloadFilesController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}
