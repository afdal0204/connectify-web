<?php
include '../config.php';

header('Content-Type: application/json; charset=utf-8');
ob_clean();

class NotificationController
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
                if (isset($_GET['type']) && $_GET['type'] === 'notification'){
                    $this->getNotifications();
                }              
                else if (isset($_GET['type']) && $_GET['type'] === 'all-notification'){
                    $this->getAllNotifications();
                } 
                else if (isset($_GET['type']) && $_GET['type'] === 'activity-log'){
                    $this->getNotificationsByUserId();
                } 
                break;
            default:
                http_response_code(405);
                echo json_encode([
                    "status" => "error",
                    "message" => "Method not allowed"
                ]);
                break;
                exit();
        }
    }
    public function getNotifications()
    {
        $result = $this->conn->query("
            SELECT * FROM notifications 
            ORDER BY created_at DESC
            LIMIT 7
        ");

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode([
            "success" => true,
            "count"   => count($data),
            "data"    => $data
        ]);
    }
    public function getAllNotifications()
    {
        $result = $this->conn->query("
            SELECT * FROM notifications 
            ORDER BY created_at DESC
            LIMIT 20
        ");

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode([
            "success" => true,
            "count" => count($data),
            "data" => $data
        ]);
    }
    public function getNotificationsByUserId()
    {
        session_start();
        $user_id = $_SESSION['user_id']; 

        $stmt = $this->conn->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 7
        ");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode([
            "success" => true,
            "count"   => count($data),
            "data"    => $data
        ]);
    }

}

// === Auto-handle access file directly ===
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new NotificationController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}