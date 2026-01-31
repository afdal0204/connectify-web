<?php
include '../config.php';

header('Content-Type: application/json; charset=utf-8');
ob_clean();

class FeedbackController {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function handle($method) {
        switch ($method) {
            case 'GET':
                $this->getAllFeedback();
                break;
            case 'POST':
                $this->feedbackUser();
                break;
            default:
                http_response_code(405);
                echo json_encode(["status" => "error", "message" => "Method not allowed"]);
                exit();
        }
    }
    
    private function getAllFeedback(){
        $result = $this->conn->query("SELECT f.id, f.feedback, u.name, u.work_id 
                                    FROM feedbacks f
                                    LEFT JOIN users u ON f.user_id = u.id
                                    ORDER BY id DESC");
        $feedback = [];
        while ($row = $result->fetch_assoc()) {
            $feedback[] = $row;
        }
        echo json_encode($feedback);
    }

    private function feedbackUser(){
        $data = json_decode(file_get_contents("php://input"), true);

        $feedback = $data['feedback'] ?? null;
        $user_id = $data['user_id'] ?? null;
        
        if (!$feedback || !$user_id ){
            http_response_code(400);
            echo json_encode(["message" => "All fields are required"]);
            exit();
        }

        $addFeedback = $this->conn->prepare("INSERT INTO feedbacks (feedback, user_id) VALUES (?,?)");
        $addFeedback->bind_param('si', $feedback, $user_id);

        if ($addFeedback->execute()){
            echo json_encode(["message" => "Feedback added successfully"]);

        }else{
            http_response_code(500);
            echo json_encode(["message" => "Failed to add feedback", "error" => $addFeedback->error]);
        }
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new FeedbackController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}