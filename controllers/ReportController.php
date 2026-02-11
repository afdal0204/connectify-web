<?php
include '../config.php';

header('Content-Type: application/json; charset=utf-8');
ob_clean();

class ReportController
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
                    $this->getTotalReports();
                } else if (isset($_GET['type']) && $_GET['type'] === 'total-by-model') {
                    $this->getTotalReportByModel();
                } else if (isset($_GET['type']) && $_GET['type'] === 'my-report') {
                    $this->getReportsByWorkId();
                } else if (isset($_GET['type']) && $_GET['type'] === 'top-reporter') {
                    $this->getTopReporter();
                } else {
                    $this->getAllReports();
                }
                break;
            case 'POST':
                $this->addReports();
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
        $filter_model      = $_GET['filter_model'] ?? '';
        $filter_date_from  = $_GET['filter_date_from'] ?? '';
        $filter_date_to    = $_GET['filter_date_to'] ?? '';

        $sql = "SELECT 
                    ar.id, ar.user_id, m.model_name, s.station_name, d.device_name, 
                    ar.shift, ar.date, ar.time_start, ar.time_finish,
                    ec.error_code, ec.symptom, ar.root_cause,
                    ar.action_taken, u.name, u.work_id, ar.remark
                FROM abnormal_reports ar
                LEFT JOIN models m ON ar.model_id = m.id
                LEFT JOIN stations s ON ar.station_id = s.id
                LEFT JOIN devices d ON ar.device_id = d.id
                LEFT JOIN error_code ec ON ar.error_code_id = ec.id
                LEFT JOIN users u ON ar.user_id = u.id
                WHERE 1 = 1";


        // --- APPLY FILTER MODEL ---
        if (!empty($filter_model)) {
            $sql .= " AND ar.model_id = " . intval($filter_model);
        }

        // --- APPLY FILTER DATE RANGE ---
        if (!empty($filter_date_from)) {
            $filter_date_from = $this->conn->real_escape_string($filter_date_from);
            $sql .= " AND ar.date >= '$filter_date_from'";
        }
        if (!empty($filter_date_to)) {
            $filter_date_to = $this->conn->real_escape_string($filter_date_to);
            $sql .= " AND ar.date <= '$filter_date_to'";
        }

        $sql .= " ORDER BY ar.date DESC, ar.time_start DESC";

        $result = $this->conn->query($sql);

        $reports = [];
        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }

        echo json_encode([
            "success" => true,
            "data"    => $reports
        ]);
        exit();
    }


    //chart and table abnormal report
    private function getTotalReportByModel()
    {
        $result = $this->conn->query("SELECT u_owner.name AS owner,
                                            m.id AS model_id,
                                            m.model_name,
                                            COUNT(ar.id) AS total_report
                                        FROM models m
                                        LEFT JOIN abnormal_reports ar ON ar.model_id = m.id
                                        LEFT JOIN users u_owner ON m.owner_id = u_owner.id
                                        GROUP BY m.id, m.model_name, u_owner.name
                                        ORDER BY model_name ASC, total_report DESC");
        $report_total = [];

        while ($row = $result->fetch_assoc()) {
            $report_total[] = $row;
        }
        echo json_encode([
            "success" => true,
            "data" => $report_total
        ]);
        exit();
    }

    private function getTotalReports()
    {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM abnormal_reports");
        $row = $result->fetch_assoc();

        echo json_encode([
            "success" => true,
            "total" => (int)$row['total']
        ]);
        exit();
    }

    public function getReportsByWorkId()
    {
        $user_id = $_GET['user_id'] ?? null;

        if (!$user_id) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing user_id"]);
            exit();
        }

        // $stmt = $this->conn->prepare("SELECT * FROM abnormal_reports WHERE user_id = ? ORDER BY `date` DESC, `time_start` DESC");
        $stmt = $this->conn->prepare("SELECT ar.id, m.model_name, s.station_name, d.device_name, 
                                        ar.shift, ar.date, ar.time_start, ar.time_finish,
                                        ec.error_code, ec.symptom, ar.root_cause,
                                        ar.action_taken, ar.user_id, u.name, u.work_id, ar.remark
                                    FROM abnormal_reports ar
                                    LEFT JOIN models m ON ar.model_id = m.id
                                    LEFT JOIN stations s ON ar.station_id = s.id
                                    LEFT JOIN devices d ON ar.device_id = d.id
                                    LEFT JOIN error_code ec ON ar.error_code_id = ec.id
                                    LEFT JOIN users u ON ar.user_id = u.id
                                    WHERE ar.user_id = ?
                                    ORDER BY ar.date DESC, ar.time_start DESC");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Prepare failed: " . $this->conn->error]);
            exit();
        }

        $stmt->bind_param("s", $user_id);

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

    public function getTopReporter()
    {
        $result = $this->conn->query(" SELECT
                                        u.id, u.name,  u.role_id,
                                        COALESCE(COUNT(c.user_id), 0) AS total_report         
                                    FROM users u
                                    LEFT JOIN (
                                        SELECT user_id FROM abnormal_reports
                                        UNION ALL
                                        SELECT user_id FROM line_report_per_shift
                                    ) AS c
                                        ON c.user_id = u.id                                   
                                    -- WHERE u.role_id IN (2, 3)                              
                                    WHERE u.role_id IN (3)                              
                                    GROUP BY u.id, u.name, u.role_id                        
                                    ORDER BY u.name ASC;        
                                ");
        // $result = $this->conn->query(" SELECT ar.id, m.model_name, ar.user_id, ar.model_id, u.name, u.work_id, COUNT(*) AS totalReports
        //                                 FROM abnormal_reports ar
        //                                 LEFT JOIN models m ON ar.model_id = m.id
        //                                 LEFT JOIN users u ON ar.user_id = u.id
        //                                 GROUP BY user_id, model_id
        //                                 ORDER BY totalReports DESC
        //                                 LIMIT 3");
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

    public function addReports()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $model_id       = (int)$data['model_id'] ?? "";
        $station_id     = (int)$data['station_id'] ?? "";
        // $device_id      = (int)$data['device_id'] ?? "";
        $device_id = isset($data['device_id']) && $data['device_id'] != "0" ? (int)$data['device_id'] : null;
        $shift          = trim($data['shift']) ?? "";
        $date           = date('Y-m-d', strtotime($data['date'])) ?? "";
        $time_start     = trim($data['time_start']) ?? "";
        $time_finish    = trim($data['time_finish']) ?? "";
        $error_code_id  = (int)$data['error_code_id'] ?? "";
        $root_cause     = ucfirst(strtolower(trim($data['root_cause']))) ?? "";
        $action_taken   = ucfirst(strtolower(trim($data['action_taken']))) ?? "";
        $user_id        = (int)$data['user_id'] ?? "";
        $remark         = ucfirst(strtolower(trim($data['remark']))) ?? "";


        if (
            !$model_id || !$station_id || !$shift || !$date || !$time_start || !$time_finish
            || !$error_code_id || !$root_cause || !$action_taken || !$user_id || !$remark
        ) {
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

        // Prepare insert statement
        $stmt = $this->conn->prepare("
            INSERT INTO abnormal_reports 
            (model_id, station_id, device_id, shift, date, time_start, time_finish,
            error_code_id, root_cause, action_taken, user_id, remark)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            'iiisssssssis',
            $model_id,
            $station_id,
            $device_id,
            $shift,
            $date,
            $time_start,
            $time_finish,
            $error_code_id,
            $root_cause,
            $action_taken,
            $user_id,
            $remark
        );

        if ($stmt->execute()) {
            $report_id = $this->conn->insert_id;
            $message = "$user_name just added new abnormal report to model $model_name";

            $notifStmt = $this->conn->prepare("
                INSERT INTO notifications (user_id, abnormal_report_id, message, model_id, is_read)
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

    private function editReport()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $id = isset($data['id']) ? intval($data['id']) : 0;

        $model_id       = (int)$data['model_id'] ?? "";
        $station_id     = (int)$data['station_id'] ?? "";
        // $device_id      = (int)$data['device_id'] ?? "";
        $device_id = isset($data['device_id']) && $data['device_id'] != "0" ? (int)$data['device_id'] : null;
        $shift          = trim($data['shift']) ?? "";
        $date           = date('Y-m-d', strtotime($data['date'])) ?? "";
        $time_start     = trim($data['time_start']) ?? "";
        $time_finish    = trim($data['time_finish']) ?? "";
        $error_code_id  = (int)$data['error_code_id'] ?? "";
        $root_cause     = ucfirst(strtolower(trim($data['root_cause']))) ?? "";
        $action_taken   = ucfirst(strtolower(trim($data['action_taken']))) ?? "";
        $remark         = ucfirst(strtolower(trim($data['remark']))) ?? "";

        if (
            !$model_id || !$station_id || !$shift || !$date || !$time_start || !$time_finish
            || !$error_code_id || !$root_cause || !$action_taken || !$remark
        ) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required"]);
            exit();
        }

        $checkStmt = $this->conn->prepare("SELECT id FROM abnormal_reports WHERE id = ?");
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

        $editReport = $this->conn->prepare("UPDATE abnormal_reports SET model_id = ?, station_id = ?, device_id = ?, shift= ?, date = ?, time_start = ?,
                                            time_finish = ?, error_code_id = ?, root_cause = ?, action_taken = ?, remark = ? WHERE id = ?");

        $editReport->bind_param(
            'iiissssisssi',
            $model_id,
            $station_id,
            $device_id,
            $shift,
            $date,
            $time_start,
            $time_finish,
            $error_code_id,
            $root_cause,
            $action_taken,
            $remark,
            $id
        );

        if ($editReport->execute()) {
            echo json_encode(["message" => "Report edited successfully"]);
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

        $checkStmt = $this->conn->prepare("SELECT id FROM abnormal_reports WHERE id = ?");
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["message" => "Report id not found"]);
            exit();
        }

        $deleteNotif = $this->conn->prepare("DELETE FROM notifications WHERE abnormal_report_id = ?");
        $deleteNotif->bind_param('i', $id);
        $deleteNotif->execute();

        $deleteReport = $this->conn->prepare("DELETE FROM abnormal_reports WHERE id = ?");
        $deleteReport->bind_param('i', $id);

        if ($deleteReport->execute()) {
            echo json_encode(["success" => true, "message" => "Report deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to delete report", "error" => $deleteReport->error]);
        }
    }
}

// === Auto-handle access file directly ===
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new ReportController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}
