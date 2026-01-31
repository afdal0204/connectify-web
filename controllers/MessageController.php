<?php
include '../config.php';

header('Content-Type: application/json; charset=utf-8');
ob_clean();

class MessageController
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
                if (isset($_GET['type']) && $_GET['type'] === 'message') {
                    $user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
                    if (!$user_id) {
                        http_response_code(400);
                        echo json_encode([
                            "success" => false,
                            "message" => "user_id is required"
                        ]);
                        exit();
                    }
                    $this->getMessages($user_id);
                }
                break;

            case 'POST':
                if (isset($_GET['type']) && $_GET['type'] === 'send') {
                    $data = json_decode(file_get_contents("php://input"), true);
                    $this->sendMessage($data);
                }
                break;

            default:
                http_response_code(405);
                echo json_encode([
                    "success" => false,
                    "message" => "Method not allowed"
                ]);
                exit();
        }
    }

    public function getMessages($user_id)
    {
        $stmt = $this->conn->prepare("
            SELECT m.id, m.sender_id, u.name AS sender_name, m.message, m.is_read, m.created_at
            FROM messages m
            LEFT JOIN users u ON m.sender_id = u.id
            WHERE m.receiver_id = ?
            ORDER BY m.created_at DESC
            LIMIT 5
        ");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode([
            "success" => true,
            "count" => count($data),
            "data" => $data
        ]);
        exit();
    }

    public function sendMessage($data)
    {
        $sender_id = $data['sender_id'] ?? null;
        $receiver_id = $data['receiver_id'] ?? null;
        $message = trim($data['message'] ?? '');

        if (!$sender_id || !$receiver_id || !$message) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "All fields are required"]);
            exit();
        }

        $stmt = $this->conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param('iis', $sender_id, $receiver_id, $message);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Message sent"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to send message"]);
        }
        exit();
    }

    public function markAsRead($message_id)
    {
        $stmt = $this->conn->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
        $stmt->bind_param('i', $message_id);
        $stmt->execute();
        echo json_encode(["success" => true]);
        exit();
    }
}

// === Auto-handle akses file langsung ===
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new MessageController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}
