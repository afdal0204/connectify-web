<?php
include '../config.php';

// === Set agar respon selalu JSON ===
header('Content-Type: application/json; charset=utf-8');
ob_clean();

class MyFilesController{
    private $conn;

    public function __construct($connection){
        $this->conn = $connection;
    }

    public function handle($method){
        switch ($method){
            case 'GET':
                $this->getAllFiles();
                break;
            case 'POST':
                $this->addNewFiles();
                break;
            case 'DELETE':
                $this->deleteFile();
                break;
            default:
                http_response_code(405);
                echo json_encode(["status" => "error", "message" => "Method not allowed"]);
                break;
                exit();
        }
    }
    private function getAllFiles(){
        $user = $_GET['id'] ?? null;

        if (!$user) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing Users"]);
            exit();
        }

        $stmt = $this->conn->prepare("SELECT files.id, 
                                            files.file_name, 
                                            files.type, 
                                            files.size,                                    
                                            files.user_id,
                                            files.created_at,
                                            users.name, 
                                            users.work_id
                                        FROM files 
                                        LEFT JOIN users ON files.user_id = users.id
                                        WHERE users.id = ?
                                        ORDER BY files.id DESC");
        
        $stmt->bind_param("s", $user);

        $stmt->execute();

        $result = $stmt->get_result(); 
        $files = [];

        while ($row = $result->fetch_assoc()) {
            $files[] = $row;
        }
        echo json_encode($files);
    }

    private function addNewFiles() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        if (!isset($_POST['user_id']) || !isset($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing required fields"]);
            return;
        }

        $user_id = intval($_POST['user_id']);
        $files = $_FILES['file']; 

        // reorganisasi array $_FILES agar lebih mudah loop
        $fileCount = is_array($files['name']) ? count($files['name']) : 1;

        $allowedExts = ['doc','docx','xls','xlsx','ppt','pptx','pdf','txt','png','jpg','jpeg','log','zip'];

        $successes = [];
        $errors = [];

        for ($i = 0; $i < $fileCount; $i++) {
            // Atur setiap file berdasarkan index (atau index 0 jika hanya satu)
            $error = is_array($files['error']) ? $files['error'][$i] : $files['error'];
            if ($error !== UPLOAD_ERR_OK) {
                $errors[] = "Upload error for file #$i: " . $error;
                continue;
            }

            $tmpName = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
            if (!is_uploaded_file($tmpName)) {
                $errors[] = "File #$i not uploaded via HTTP POST";
                continue;
            }

            $file_name = is_array($files['name']) ? $files['name'][$i] : $files['name'];
            $fileTypeClient = is_array($files['type']) ? $files['type'][$i] : $files['type'];
            $fileSize = is_array($files['size']) ? $files['size'][$i] : $files['size'];
            $fileExt = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($fileExt, $allowedExts)) {
                $errors[] = "File type not allowed for file '$file_name': .$fileExt";
                continue;
            }

            $fileData = file_get_contents($tmpName);
            if ($fileData === false) {
                $errors[] = "Failed to read file data for file '$file_name'";
                continue;
            }

            error_log("Uploading file: $file_name, size: $fileSize, type: $fileTypeClient");

            // Query insert
            $sql = "INSERT INTO files (file_name, type, size, user_id, file_data) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                $errors[] = "Prepare failed for file '$file_name': " . $this->conn->error;
                continue;
            }

            // Bind parameter (blob) — letakkan placeholder NULL di posisi blob
            $null = NULL;
            $stmt->bind_param("ssisb", $file_name, $fileTypeClient, $fileSize, $user_id, $null);
            // send_long_data untuk blob
            $paramBlobIndex = 4;  // parameter ke‑5 (0-based)
            $chunkSize = 8192;
            $offset = 0;
            $len = strlen($fileData);
            while ($offset < $len) {
                $chunk = substr($fileData, $offset, $chunkSize);
                $stmt->send_long_data($paramBlobIndex, $chunk);
                $offset += $chunkSize;
            }

            if ($stmt->execute()) {
                $successes[] = $file_name;
            } else {
                $errors[] = "Database error for file '$file_name': " . $stmt->error;
            }

            $stmt->close();
        }

        // Respon JSON dengan daftar sukses & error
        if (count($errors) === 0) {
            echo json_encode(["status" => "success", "message" => "All files uploaded successfully", "files" => $successes]);
        } else if (count($successes) > 0) {
            http_response_code(207);  // 207 Multi-Status
            echo json_encode(["status" => "partial", "message" => "Some files failed", "success" => $successes, "errors" => $errors]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Upload failed", "errors" => $errors]);
        }
    }
    private function deleteFile() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "File not provided"]);
            exit;
        }
        $id = intval($input['id']);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid id"]);
            exit;
        }

        $stmt = $this->conn->prepare("DELETE FROM files WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "File deleted"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $stmt->error]);
        }
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new MyFilesController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}
