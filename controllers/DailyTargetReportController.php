<?php
include '../config.php';

header('Content-Type: application/json; charset=utf-8');
ob_clean();

class DailyTargetReportController
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
                    $this->getTotalTargetReports();
                }
                else if (isset($_GET['type']) && $_GET['type'] === 'target-report-chart') {
                    $this->getTargetReport();
                } else {
                    $this->getAllReports();
                }
                break;
            case 'POST':
                $this->addTargetReports();
                break;
            case 'PUT':
                $this->editReport();
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

    public function getAllReports()
    {
        $result = $this->conn->query("SELECT dtr.id, dtr.date, dtr.remark, dtr.target, dtr.output, dtr.gap,
                                            dtr.user_id, m.model_name, m.line_area, 
                                            us.uph_status_name, 
                                            u.name AS report_user,
                                            u_owner.name AS owner_name,
                                            d.department_name,
                                            dtr.uph_status_id
                                        FROM daily_target_report dtr
                                        LEFT JOIN models m ON dtr.model_id = m.id
                                        LEFT JOIN users u ON dtr.user_id = u.id              -- user created report
                                        LEFT JOIN users u_owner ON m.owner_id = u_owner.id    -- owner model (get department)
                                        LEFT JOIN department d ON u_owner.department_id = d.id
                                        LEFT JOIN uph_status us ON dtr.uph_status_id = us.id
                                        ORDER BY dtr.date DESC, dtr.id DESC;
                                        ");
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

    private function getTotalTargetReports()
    {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM daily_target_report");
        $row = $result->fetch_assoc();

        echo json_encode([
            "success" => true,
            "total" => (int)$row['total']
        ]);
        exit();
    }
      
    // target report chart
    private function getTargetReport(){
        $result = $this->conn->query("SELECT 
                                            dtr.date,
                                            m.model_name,
                                            us.uph_status_name
                                        FROM daily_target_report dtr
                                        LEFT JOIN models m ON dtr.model_id = m.id
                                        LEFT JOIN uph_status us ON dtr.uph_status_id = us.id
                                        GROUP BY 
                                            dtr.date,
                                            m.model_name,
                                            us.uph_status_name
                                        ORDER BY dtr.date ASC, m.model_name ASC");
        $dailytarget = [];

        while ($row = $result->fetch_assoc()) {
            $dailytarget[] = $row;
        }
        echo json_encode([
            "success" => true,
            "data" => $dailytarget
        ]);
        exit();
    }

    public function addTargetReports() {
        $data = json_decode(file_get_contents("php://input"), true);

        $date = isset($data['date']) ? date('Y-m-d', strtotime($data['date'])) : "";
        $model_id = isset($data['model_id']) ? (int)$data['model_id'] : null;
        $uph_status_id = isset($data['uph_status_id']) ? (int)$data['uph_status_id'] : null;
        $target = isset($data['target']) ? (int)$data['target'] : null;
        $output = isset($data['output']) ? (int)$data['output'] : null;
        $gap = isset($data['gap']) ? $data['gap'] : null;
        $user_id = isset($data['user_id']) ? (int)$data['user_id'] : null;
        $remark = isset($data['remark']) ? ucfirst(strtolower(trim($data['remark']))) : null;

        // if (!$date || !$model_id || !$uph_status_id || $target === null || $output === null || $user_id === null || $gap === null) {
        if (!$date || !$model_id || !$uph_status_id|| $user_id === null) {
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

        $stmt = $this->conn->prepare("INSERT INTO daily_target_report (date, model_id, uph_status_id, target, output, gap, user_id, remark)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('siiiisis',
                $date,
                $model_id,
                $uph_status_id,
                $target,
                $output,
                $gap, 
                $user_id,
                $remark
            );
            
        if ($stmt->execute()) {
            // $report_id = $this->conn->insert_id;
            // $message = "$user_name just added new daily target report to model $model_name";

            // $notifStmt = $this->conn->prepare("
            //     INSERT INTO notifications (user_id, target_report_id, message, model_id, is_read)
            //     VALUES (?, ?, ?, ?, 0)
            // ");
            // $notifStmt->bind_param("iisi", $user_id, $report_id, $message, $model_id);
            // $notifStmt->execute();

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


    private function editReport()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $id = isset($data['id']) ? intval($data['id']) : 0;

        $date = isset($data['date']) ? date('Y-m-d', strtotime($data['date'])) : "";
        // $model_id = isset($data['model_id']) ? (int)$data['model_id'] : null;
        $uph_status_id = isset($data['uph_status_id']) ? (int)$data['uph_status_id'] : null;
        $target = isset($data['target']) ? (int)$data['target'] : null;
        $output = isset($data['output']) ? (int)$data['output'] : null;
        $gap = isset($data['gap']) ? $data['gap'] : null;
        // $user_id = isset($data['user_id']) ? (int)$data['user_id'] : null;
        $remark = isset($data['remark']) ? ucfirst(strtolower(trim($data['remark']))) : null;

        // if (!$id
        //     || !$date
        //     || $uph_status_id === null
        //     || $uph_status_id <= 0
        //     || $target === null
        //     || $output === null
        //     || $gap === null || $gap === "") {
        //     http_response_code(400);
        //     echo json_encode(["message" => "All fields are required"]);
        //     exit();
        // }
        if (!$id
            || !$date
            || $uph_status_id === null
            || $uph_status_id <= 0) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required"]);
            exit();
        }

        $checkStmt = $this->conn->prepare("SELECT id FROM daily_target_report WHERE id = ?");
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Report not found"
            ]);
            exit();
        }

        $row = $result->fetch_assoc();
        $id = $row['id'];

        $editReport = $this->conn->prepare("UPDATE daily_target_report SET date = ?, uph_status_id = ?, 
                                            target = ?, output = ?, gap = ?, remark = ? WHERE id = ?");

        $editReport->bind_param('siiissi',
                $date,
                $uph_status_id,
                $target,
                $output,
                $gap, 
                $remark,
                $id
            );

        if ($editReport->execute()) {
            echo json_encode(["success" => true, "message" => "Report edited successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to edit report", "error" => $editReport->error]);
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

        $checkStmt = $this->conn->prepare("SELECT id FROM daily_target_report WHERE id = ?");
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["message" => "Report id not found"]);
            exit();
        }

        $deleteNotif = $this->conn->prepare("DELETE FROM notifications WHERE target_report_id = ?");
        $deleteNotif->bind_param('i', $id);
        $deleteNotif->execute();

        $deleteReport = $this->conn->prepare("DELETE FROM daily_target_report WHERE id = ?");
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
    $controller = new DailyTargetReportController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}
