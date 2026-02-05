<?php
include '../config.php';

header('Content-Type: application/json; charset=utf-8');
ob_clean();

class LineReportController
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
                if (isset($_GET['type']) && $_GET['type'] === 'total') {
                    $this->getLineReportsTotal();
                }
                else if (isset($_GET['type']) && $_GET['type'] === 'sec1') {
                    $this->getAllReportsSec1(1);
                }
                else if (isset($_GET['type']) && $_GET['type'] === 'sec2') {
                    $this->getAllReportsSec2(2);
                }
                else if (isset($_GET['type']) && $_GET['type'] === 'sec3') {
                    $this->getAllReportsSec3(3);
                }
                break;
            case 'POST':
                $this->addLineReports();
                break;
            case 'DELETE':
                $this->deleteReports();
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

    public function getAllReportsSec1($departmentId)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                lrp.id, 
                lrp.shift, 
                lrp.user_id,
                lrp.date, 
                lrp.remark,
                m.model_name, 
                m.line_area,
                u.name AS report_user,
                d.department_name
            FROM line_report_per_shift lrp
            LEFT JOIN models m ON lrp.model_id = m.id
            LEFT JOIN users u ON lrp.user_id = u.id
            LEFT JOIN users u_owner ON m.owner_id = u_owner.id
            LEFT JOIN department d ON u_owner.department_id = d.id
            WHERE d.id = ?
            ORDER BY lrp.date DESC, lrp.id DESC
        ");

        $stmt->bind_param("i", $departmentId);
        $stmt->execute();

        $result = $stmt->get_result();
        $reports = [];

        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }

        echo json_encode([
            "success" => true,
            "data" => $reports
        ]);
        exit();
    }

    public function getAllReportsSec2($departmentId)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                lrp.id, 
                lrp.shift, 
                lrp.user_id,
                lrp.date, 
                lrp.remark,
                m.model_name, 
                m.line_area,
                u.name AS report_user,
                d.department_name
            FROM line_report_per_shift lrp
            LEFT JOIN models m ON lrp.model_id = m.id
            LEFT JOIN users u ON lrp.user_id = u.id
            LEFT JOIN users u_owner ON m.owner_id = u_owner.id
            LEFT JOIN department d ON u_owner.department_id = d.id
            WHERE d.id = ?
            ORDER BY lrp.date DESC, lrp.id DESC
        ");

        $stmt->bind_param("i", $departmentId);
        $stmt->execute();

        $result = $stmt->get_result();
        $reports = [];

        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }

        echo json_encode([
            "success" => true,
            "data" => $reports
        ]);
        exit();
    }
    public function getAllReportsSec3($departmentId)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                lrp.id, 
                lrp.shift, 
                lrp.user_id,
                lrp.date, 
                lrp.remark,
                m.model_name, 
                m.line_area,
                u.name AS report_user,
                d.department_name
            FROM line_report_per_shift lrp
            LEFT JOIN models m ON lrp.model_id = m.id
            LEFT JOIN users u ON lrp.user_id = u.id
            LEFT JOIN users u_owner ON m.owner_id = u_owner.id
            LEFT JOIN department d ON u_owner.department_id = d.id
            WHERE d.id = ?
            ORDER BY lrp.date DESC, lrp.id DESC
        ");

        $stmt->bind_param("i", $departmentId);
        $stmt->execute();

        $result = $stmt->get_result();
        $reports = [];

        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }

        echo json_encode([
            "success" => true,
            "data" => $reports
        ]);
        exit();
    }

    private function getLineReportsTotal()
    {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM line_report_per_shift");
        $row = $result->fetch_assoc();

        echo json_encode([
            "success" => true,
            "total" => (int)$row['total']
        ]);
        exit();
    }

    public function addLineReports() {
        $data = json_decode(file_get_contents("php://input"), true);

        $date = isset($data['date']) ? date('Y-m-d', strtotime($data['date'])) : "";
        $shift = trim($data['shift']) ?? "";
        $model_id = isset($data['model_id']) ? (int)$data['model_id'] : null;
        $user_id = isset($data['user_id']) ? (int)$data['user_id'] : null;
        // $remark = isset($data['remark']) ? ucfirst(strtolower(trim($data['remark']))) : null;
        $remark = trim($data['remark']) ?? "";

        if (!$shift || !$date || !$model_id || $user_id === null) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required"]);
            exit();
        }

        // ========= AMBIL NAMA USER =========
        $userQuery = $this->conn->query("SELECT name FROM users WHERE id = $user_id");
        $user = $userQuery->fetch_assoc();
        $user_name = $user['name'] ?? 'Unknown User';

        // ========= AMBIL NAMA MODEL =========
        $modelQuery = $this->conn->query("SELECT model_name FROM models WHERE id = $model_id");
        $model = $modelQuery->fetch_assoc();
        $model_name = $model['model_name'] ?? 'Unknown Model';

        $stmt = $this->conn->prepare("INSERT INTO line_report_per_shift (shift, date, model_id, user_id, remark)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('ssiis',
                $shift,
                $date,
                $model_id, 
                $user_id,
                $remark
            );
            
        if ($stmt->execute()) {
            $report_id = $this->conn->insert_id;
            $message = "$user_name just added new line report per shift to model $model_name";

            $notifStmt = $this->conn->prepare("
                INSERT INTO notifications (user_id, line_report_id, message, model_id, is_read)
                VALUES (?, ?, ?, ?, 0)
            ");
            $notifStmt->bind_param("iisi", $user_id, $report_id, $message, $model_id);
            $notifStmt->execute();

            echo json_encode(["success" => true, "message" => "New report added successfully"]);
            exit();
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to add report",
                "error" => $stmt->error
            ]);
        }
    }

    public function deleteReports()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(["message" => "Report ID is required"]);
            exit();
        }

        $checkStmt = $this->conn->prepare("SELECT id FROM line_report_per_shift WHERE id = ?");
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["message" => "Report id not found"]);
            exit();
        }

        $deleteNotif = $this->conn->prepare("DELETE FROM notifications WHERE line_report_id = ?");
        $deleteNotif->bind_param('i', $id);
        $deleteNotif->execute();

        $deleteReport = $this->conn->prepare("DELETE FROM line_report_per_shift WHERE id = ?");
        $deleteReport->bind_param('i', $id);

        if ($deleteReport->execute()) {
            echo json_encode(["success"=>true, "message" => "Report deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to delete report", "error" => $deleteReport->error]);
        }
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new LineReportController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}
