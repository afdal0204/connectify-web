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
                if (isset($_GET['type']) && $_GET['type'] === 'QA-report') {
                    $this->getAllQAReports();
                } else if (isset($_GET['type']) && $_GET['type'] === 'SQE-report') {
                    $this->getAllSQEReports();
                } else if (isset($_GET['type']) && $_GET['type'] === 'PD-report') {
                    $this->getAllPDReports();
                } else if (isset($_GET['type']) && $_GET['type'] === 'FE-report') {
                    $this->getAllFEReports();
                } else if (isset($_GET['type']) && $_GET['type'] === 'FME-report') {
                    $this->getAllFMEReports();
                } else {
                    $this->getAllReports();
                }
                break;

            case 'POST':
                // $data = json_decode(file_get_contents("php://input"), true);
                $data = $_POST;
                // ❗ validasi JSON
                if (!$data) {
                    http_response_code(400);
                    echo json_encode(["message" => "Invalid JSON"]);
                    exit();
                }

                // ❗ ambil type aman
                $type = $data['type'] ?? null;

                if (!$type) {
                    http_response_code(400);
                    echo json_encode(["message" => "Type is required"]);
                    exit();
                }

                // ❗ optional: support kalau data berupa array (bulk insert)
                if (isset($data[0])) {
                    $payload = $data;
                } else {
                    $payload = [$data]; // samakan jadi array biar konsisten
                }

                switch ($type) {
                    case 'QA-report':
                        foreach ($payload as $row) {
                            $this->addQAReports($row);
                        }
                        break;

                    case 'SQE-report':
                        foreach ($payload as $row) {
                            $this->addSQEReports($row);
                        }
                        break;

                    case 'PD-report':
                        foreach ($payload as $row) {
                            $this->addPDReports($row);
                        }
                        break;

                    case 'FE-report':
                        foreach ($payload as $row) {
                            $this->addFEReports($row);
                        }
                        break;

                    case 'FME-report':
                        foreach ($payload as $row) {
                            $this->addFMEReports($row);
                        }
                        break;

                    default:
                        http_response_code(400);
                        echo json_encode(["message" => "Unknown type"]);
                        break;
                }
                break;

            case 'PUT':
                $data = json_decode(file_get_contents("php://input"), true);

                switch ($data['type'] ?? '') {

                    case 'update-status':
                        $this->updateStatus($data);
                        break;
                }
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
        $filter_dept       = $_GET['filter_dept'] ?? '';
        $filter_model      = $_GET['filter_model'] ?? '';
        $filter_station    = $_GET['filter_station'] ?? '';
        $filter_device     = $_GET['filter_device'] ?? '';
        $filter_date_from  = $_GET['filter_date_from'] ?? '';
        $filter_date_to    = $_GET['filter_date_to'] ?? '';

        $sql = "SELECT
                    ar.id, ar.user_id, m.model_name, m.line_area, s.station_name, d.device_name,
                    ar.shift, ar.date, ar.time_start, ar.time_finish,
                    ec.error_code, ec.symptom, ar.root_cause,
                    ar.action_taken, u.name, u.work_id,
                    dept.department_name,
                    dept.remark as dept_remark,
                    ar.failure_photo, ar.input_quantity, ar.defect_quantity, ar.failure_rate,
                    ar.short_term_solution, ar.long_term_solution, ar.preventive_action,
                    ar.responsible_person, ar.status, ar.improvement_photo, ar.remark,
                    ar.created_at, ar.updated_at
                FROM multi_dept_reports ar
                LEFT JOIN models m ON ar.model_id = m.id
                LEFT JOIN stations s ON ar.station_id = s.id
                LEFT JOIN devices d ON ar.device_id = d.id
                LEFT JOIN error_code ec ON ar.error_code_id = ec.id
                LEFT JOIN users u ON ar.user_id = u.id
                LEFT JOIN users u_owner ON m.owner_id = u_owner.id
                -- LEFT JOIN department dept ON m.owner_id = u_owner.id AND u_owner.department_id = dept.id
                LEFT JOIN department dept ON u.department_id = dept.id -- base on user department
                WHERE dept.remark = 'PE' AND 1 = 1";


        // --- APPLY FILTER DEPARTMENT ---
        if (!empty($filter_dept)) {
            $sql .= " AND dept.id = " . intval($filter_dept);
        }

        // --- APPLY FILTER MODEL ---
        if (!empty($filter_model)) {
            $sql .= " AND ar.model_id = " . intval($filter_model);
        }

        // --- APPLY FILTER STATION ---
        if (!empty($filter_station)) {
            $sql .= " AND ar.station_id = " . intval($filter_station);
        }

        // --- APPLY FILTER DEVICE ---
        if (!empty($filter_device)) {
            $sql .= " AND ar.device_id = " . intval($filter_device);
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

    public function getAllQAReports()
    {
        $filter_dept       = $_GET['filter_dept'] ?? '';
        $filter_model      = $_GET['filter_model'] ?? '';
        $filter_station    = $_GET['filter_station'] ?? '';
        $filter_device     = $_GET['filter_device'] ?? '';
        $filter_date_from  = $_GET['filter_date_from'] ?? '';
        $filter_date_to    = $_GET['filter_date_to'] ?? '';

        $sql = "SELECT
                    ar.id, ar.user_id, m.model_name, m.line_area, s.station_name, d.device_name,
                    ar.shift, ar.date, ar.time_start, ar.time_finish,
                    ec.error_code, ec.symptom, ar.root_cause,
                    ar.action_taken, u.name, u.work_id,
                    dept.department_name,
                    dept.remark as dept_remark,
                    ar.failure_photo, ar.input_quantity, ar.defect_quantity, ar.failure_rate,
                    ar.short_term_solution, ar.long_term_solution, ar.preventive_action,
                    ar.responsible_person, ar.status, ar.improvement_photo, ar.remark,
                    ar.created_at, ar.updated_at
                FROM multi_dept_reports ar
                LEFT JOIN models m ON ar.model_id = m.id
                LEFT JOIN stations s ON ar.station_id = s.id
                LEFT JOIN devices d ON ar.device_id = d.id
                LEFT JOIN error_code ec ON ar.error_code_id = ec.id
                LEFT JOIN users u ON ar.user_id = u.id
                LEFT JOIN users u_owner ON m.owner_id = u_owner.id
                -- LEFT JOIN department dept ON m.owner_id = u_owner.id AND u_owner.department_id = dept.id
                LEFT JOIN department dept ON u.department_id = dept.id -- base on user department
                WHERE dept.remark = 'QA' AND 1 = 1";

        // --- APPLY FILTER DEPARTMENT ---
        if (!empty($filter_dept)) {
            $sql .= " AND dept.id = " . intval($filter_dept);
        }

        // --- APPLY FILTER MODEL ---
        if (!empty($filter_model)) {
            $sql .= " AND ar.model_id = " . intval($filter_model);
        }

        // --- APPLY FILTER STATION ---
        if (!empty($filter_station)) {
            $sql .= " AND ar.station_id = " . intval($filter_station);
        }

        // --- APPLY FILTER DEVICE ---
        if (!empty($filter_device)) {
            $sql .= " AND ar.device_id = " . intval($filter_device);
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

        $sql .= " ORDER BY ar.id DESC, ar.date DESC, ar.time_start DESC";

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
    public function getAllSQEReports()
    {
        $filter_dept       = $_GET['filter_dept'] ?? '';
        $filter_model      = $_GET['filter_model'] ?? '';
        $filter_station    = $_GET['filter_station'] ?? '';
        $filter_device     = $_GET['filter_device'] ?? '';
        $filter_date_from  = $_GET['filter_date_from'] ?? '';
        $filter_date_to    = $_GET['filter_date_to'] ?? '';

        $sql = "SELECT
                    ar.id, ar.user_id,
                    m.model_name, m.line_area,
                    ar.date, ar.item,
                    ar.highlight_from, ar.customer,
                    ar.product_number, ar.supplier,
                    ar.issue, ar.issue_description,
                    ar.root_cause,
                    ar.short_term_solution, ar.long_term_solution,
                    ar.responsible_person, ar.status,
                    ar.failure_rate, ar.stock,
                    ar.immediately_action, ar.sorting_rework,
                    ar.8d_report_received_day, ar.action_lot,
                    ar.remark, ar.btc_no,
                    dept.department_name,
                    dept.remark as dept_remark,
                    u.name AS sqe_owner, u.work_id,
                    ar.created_at, ar.updated_at
                FROM multi_dept_reports ar
                LEFT JOIN models m ON ar.model_id = m.id
                LEFT JOIN stations s ON ar.station_id = s.id
                LEFT JOIN devices d ON ar.device_id = d.id
                LEFT JOIN error_code ec ON ar.error_code_id = ec.id
                LEFT JOIN users u ON ar.user_id = u.id
                LEFT JOIN users u_owner ON m.owner_id = u_owner.id
                LEFT JOIN department dept ON u.department_id = dept.id
                WHERE dept.remark = 'SQE' AND 1 = 1";

        // --- APPLY FILTER DEPARTMENT ---
        if (!empty($filter_dept)) {
            $sql .= " AND dept.id = " . intval($filter_dept);
        }

        // --- APPLY FILTER MODEL ---
        if (!empty($filter_model)) {
            $sql .= " AND ar.model_id = " . intval($filter_model);
        }

        // --- APPLY FILTER STATION ---
        if (!empty($filter_station)) {
            $sql .= " AND ar.station_id = " . intval($filter_station);
        }

        // --- APPLY FILTER DEVICE ---
        if (!empty($filter_device)) {
            $sql .= " AND ar.device_id = " . intval($filter_device);
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
    public function getAllPDReports()
    {
        $filter_dept       = $_GET['filter_dept'] ?? '';
        $filter_model      = $_GET['filter_model'] ?? '';
        $filter_station    = $_GET['filter_station'] ?? '';
        $filter_device     = $_GET['filter_device'] ?? '';
        $filter_date_from  = $_GET['filter_date_from'] ?? '';
        $filter_date_to    = $_GET['filter_date_to'] ?? '';

        $sql = "SELECT
                    ar.id, ar.user_id, m.model_name, m.line_area, s.station_name, d.device_name,
                    ar.shift, ar.date, ar.time_start, ar.time_finish,
                    ec.error_code, ec.symptom, ar.root_cause,
                    ar.action_taken, u.name, u.work_id,
                    dept.department_name,
                    dept.remark as dept_remark,
                    ar.failure_photo, ar.input_quantity, ar.defect_quantity, ar.failure_rate,
                    ar.short_term_solution, ar.long_term_solution, ar.preventive_action,
                    ar.responsible_person, ar.status, ar.improvement_photo, ar.remark,
                    ar.created_at, ar.updated_at
                FROM multi_dept_reports ar
                LEFT JOIN models m ON ar.model_id = m.id
                LEFT JOIN stations s ON ar.station_id = s.id
                LEFT JOIN devices d ON ar.device_id = d.id
                LEFT JOIN error_code ec ON ar.error_code_id = ec.id
                LEFT JOIN users u ON ar.user_id = u.id
                LEFT JOIN users u_owner ON m.owner_id = u_owner.id
                -- LEFT JOIN department dept ON m.owner_id = u_owner.id AND u_owner.department_id = dept.id
                LEFT JOIN department dept ON u.department_id = dept.id -- base on user department
                WHERE dept.remark = 'PD' AND 1 = 1";

        // --- APPLY FILTER DEPARTMENT ---
        if (!empty($filter_dept)) {
            $sql .= " AND dept.id = " . intval($filter_dept);
        }

        // --- APPLY FILTER MODEL ---
        if (!empty($filter_model)) {
            $sql .= " AND ar.model_id = " . intval($filter_model);
        }

        // --- APPLY FILTER STATION ---
        if (!empty($filter_station)) {
            $sql .= " AND ar.station_id = " . intval($filter_station);
        }

        // --- APPLY FILTER DEVICE ---
        if (!empty($filter_device)) {
            $sql .= " AND ar.device_id = " . intval($filter_device);
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
    public function getAllFEReports()
    {
        $filter_dept       = $_GET['filter_dept'] ?? '';
        $filter_model      = $_GET['filter_model'] ?? '';
        $filter_station    = $_GET['filter_station'] ?? '';
        $filter_device     = $_GET['filter_device'] ?? '';
        $filter_date_from  = $_GET['filter_date_from'] ?? '';
        $filter_date_to    = $_GET['filter_date_to'] ?? '';

        $sql = "SELECT
                    ar.id, ar.user_id, m.model_name, m.line_area, s.station_name, d.device_name,
                    ar.shift, ar.date, ar.time_start, ar.time_finish,
                    ec.error_code, ec.symptom, ar.root_cause,
                    ar.action_taken, u.name, u.work_id,
                    dept.department_name,
                    dept.remark as dept_remark,
                    ar.failure_photo, ar.input_quantity, ar.defect_quantity, ar.failure_rate,
                    ar.short_term_solution, ar.long_term_solution, ar.preventive_action,
                    ar.responsible_person, ar.status, ar.improvement_photo, ar.remark,
                    ar.created_at, ar.updated_at
                FROM multi_dept_reports ar
                LEFT JOIN models m ON ar.model_id = m.id
                LEFT JOIN stations s ON ar.station_id = s.id
                LEFT JOIN devices d ON ar.device_id = d.id
                LEFT JOIN error_code ec ON ar.error_code_id = ec.id
                LEFT JOIN users u ON ar.user_id = u.id
                LEFT JOIN users u_owner ON m.owner_id = u_owner.id
                LEFT JOIN department dept ON u.department_id = dept.id
                WHERE dept.remark = 'FE' || dept.remark = 'FME' AND 1 = 1";

        // --- APPLY FILTER DEPARTMENT ---
        if (!empty($filter_dept)) {
            $sql .= " AND dept.id = " . intval($filter_dept);
        }

        // --- APPLY FILTER MODEL ---
        if (!empty($filter_model)) {
            $sql .= " AND ar.model_id = " . intval($filter_model);
        }

        // --- APPLY FILTER STATION ---
        if (!empty($filter_station)) {
            $sql .= " AND ar.station_id = " . intval($filter_station);
        }

        // --- APPLY FILTER DEVICE ---
        if (!empty($filter_device)) {
            $sql .= " AND ar.device_id = " . intval($filter_device);
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
    public function getAllFMEReports()
    {
        $filter_dept       = $_GET['filter_dept'] ?? '';
        $filter_model      = $_GET['filter_model'] ?? '';
        $filter_station    = $_GET['filter_station'] ?? '';
        $filter_device     = $_GET['filter_device'] ?? '';
        $filter_date_from  = $_GET['filter_date_from'] ?? '';
        $filter_date_to    = $_GET['filter_date_to'] ?? '';

        $sql = "SELECT
                    ar.id, ar.user_id, m.model_name, m.line_area, s.station_name, d.device_name,
                    ar.shift, ar.date, ar.time_start, ar.time_finish,
                    ec.error_code, ec.symptom, ar.root_cause,
                    ar.action_taken, u.name, u.work_id,
                    dept.department_name,
                    dept.remark as dept_remark,
                    ar.failure_photo, ar.input_quantity, ar.defect_quantity, ar.failure_rate,
                    ar.short_term_solution, ar.long_term_solution, ar.preventive_action,
                    ar.responsible_person, ar.status, ar.improvement_photo, ar.remark,
                    ar.created_at, ar.updated_at
                FROM multi_dept_reports ar
                LEFT JOIN models m ON ar.model_id = m.id
                LEFT JOIN stations s ON ar.station_id = s.id
                LEFT JOIN devices d ON ar.device_id = d.id
                LEFT JOIN error_code ec ON ar.error_code_id = ec.id
                LEFT JOIN users u ON ar.user_id = u.id
                LEFT JOIN users u_owner ON m.owner_id = u_owner.id
                -- LEFT JOIN department dept ON m.owner_id = u_owner.id AND u_owner.department_id = dept.id
                LEFT JOIN department dept ON u.department_id = dept.id -- base on user department
                WHERE dept.remark = 'FME' AND 1 = 1";

        // --- APPLY FILTER DEPARTMENT ---
        if (!empty($filter_dept)) {
            $sql .= " AND dept.id = " . intval($filter_dept);
        }

        // --- APPLY FILTER MODEL ---
        if (!empty($filter_model)) {
            $sql .= " AND ar.model_id = " . intval($filter_model);
        }

        // --- APPLY FILTER STATION ---
        if (!empty($filter_station)) {
            $sql .= " AND ar.station_id = " . intval($filter_station);
        }

        // --- APPLY FILTER DEVICE ---
        if (!empty($filter_device)) {
            $sql .= " AND ar.device_id = " . intval($filter_device);
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

    public function addQAReports()
    {
        $data = $_POST;

        $model_id       = isset($data['model_id']) ? (int)$data['model_id'] : 0;
        $station_id     = isset($data['station_id']) ? (int)$data['station_id'] : 0;
        $device_id      = isset($data['device_id']) && $data['device_id'] != "0" ? (int)$data['device_id'] : null;
        $shift          = $data['shift'] ?? null;
        $date           = !empty($data['date']) ? date('Y-m-d', strtotime($data['date'])) : null;

        $error_code_id      = isset($data['error_code_id']) ? (int)$data['error_code_id'] : null;
        $input_quantity     = isset($data['input_quantity']) ? (int)$data['input_quantity'] : 0;
        $defect_quantity    = isset($data['defect_quantity']) ? (int)$data['defect_quantity'] : 0;
        $failure_rate       = $data['failure_rate'] ?? null;

        $root_cause             = $data['root_cause'] ?? null;
        $short_term_solution    = $data['short_term_solution'] ?? null;
        $long_term_solution     = $data['long_term_solution'] ?? null;

        $responsible_person = $data['responsible_person'] ?? null;
        $status             = $data['status'] ?? null;

        $user_id = isset($data['user_id']) ? (int)$data['user_id'] : 0;
        $remark  = $data['remark'] ?? null;

        if (!$model_id || !$station_id || !$user_id) {
            http_response_code(400);
            echo json_encode(["message" => "All required fields must be filled"]);
            exit();
        }

        // =====================================
        // 3. UPLOAD FILE (FAILURE PHOTO)
        // =====================================
        $failure_photo = null;

        if (isset($_FILES['failure_photo']) && $_FILES['failure_photo']['error'] === 0) {
            $uploadDir = __DIR__ . "/../uploads/failure_photos/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . "_" . uniqid() . "." . pathinfo($_FILES['failure_photo']['name'], PATHINFO_EXTENSION);

            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['failure_photo']['tmp_name'], $targetPath)) {
                $failure_photo = "uploads/failure_photos/" . $fileName;
            }
        }

        $userQuery = $this->conn->query("SELECT name FROM users WHERE id = $user_id");
        $user = $userQuery->fetch_assoc();
        $user_name = $user['name'] ?? 'Unknown User';

        $modelQuery = $this->conn->query("SELECT model_name FROM models WHERE id = $model_id");
        $model = $modelQuery->fetch_assoc();
        $model_name = $model['model_name'] ?? 'Unknown Model';

        $stmt = $this->conn->prepare("
            INSERT INTO multi_dept_reports
            (model_id, station_id, device_id, shift, date,
            input_quantity, defect_quantity, failure_rate,
            error_code_id, root_cause, short_term_solution, long_term_solution,
            responsible_person, status,
            user_id, remark, failure_photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            'iiissiiissssssiss',
            $model_id,
            $station_id,
            $device_id,
            $shift,
            $date,
            $input_quantity,
            $defect_quantity,
            $failure_rate,
            $error_code_id,
            $root_cause,
            $short_term_solution,
            $long_term_solution,
            $responsible_person,
            $status,
            $user_id,
            $remark,
            $failure_photo
        );

        if ($stmt->execute()) {

            $report_id = $this->conn->insert_id;
            $message = "$user_name just added new QA report to model $model_name";

            $notifStmt = $this->conn->prepare("
                INSERT INTO notifications (user_id, multi_dept_reports_id, message, model_id, is_read)
                VALUES (?, ?, ?, ?, 0)
            ");

            $notifStmt->bind_param("iisi", $user_id, $report_id, $message, $model_id);
            $notifStmt->execute();

            echo json_encode([
                "success" => true,
                "message" => "New report added successfully"
            ]);
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

    public function addSQEReports()
    {
        // Support both form-data ($_POST) and raw JSON
        $data = $_POST;
        if (empty($data) || !isset($data['type'])) {
            $json = json_decode(file_get_contents("php://input"), true);
            if ($json) {
                $data = $json;
            }
        }

        $model_id       = isset($data['model_id']) ? (int)$data['model_id'] : 0;
        $date           = !empty($data['date']) ? date('Y-m-d', strtotime($data['date'])) : null;
        $failure_rate   = $data['failure_rate'] ?? null;
        $item           = $data['item'] ?? null;

        $highlight_from         = $data['highlight_from'] ?? null;
        $customer               = $data['customer'] ?? null;
        $product_number         = $data['product_number'] ?? null;
        $supplier               = $data['supplier'] ?? null;
        $issue                  = $data['issue'] ?? null;
        $issue_description      = $data['issue_description'] ?? null;
        $stock                  = $data['stock'] ?? null;
        $immediately_action     = $data['immediately_action'] ?? null;
        $sorting_rework         = $data['sorting_rework'] ?? null;
        $action_lot             = $data['action_lot'] ?? null;
        $btc_no                 = $data['btc_no'] ?? null;

        $root_cause             = $data['root_cause'] ?? null;
        $short_term_solution    = $data['short_term_solution'] ?? null;
        $long_term_solution     = $data['long_term_solution'] ?? null;

        $responsible_person = $data['responsible_person'] ?? null;
        $status             = $data['status'] ?? null;

        $user_id = isset($data['user_id']) ? (int)$data['user_id'] : 0;
        $remark  = $data['remark'] ?? null;

        $report_received_day = null;
        if (!empty($data['8d_report_received_day'])) {
            $report_received_day = date('Y-m-d', strtotime($data['8d_report_received_day']));
        }

        if (!$model_id || !$user_id) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "All required fields must be filled"]);
            exit();
        }

        $userQuery = $this->conn->query("SELECT name FROM users WHERE id = $user_id");
        $user = $userQuery->fetch_assoc();
        $user_name = $user['name'] ?? 'Unknown User';

        $modelQuery = $this->conn->query("SELECT model_name FROM models WHERE id = $model_id");
        $model = $modelQuery->fetch_assoc();
        $model_name = $model['model_name'] ?? 'Unknown Model';

        // Prepare insert statement
        $stmt = $this->conn->prepare("
            INSERT INTO multi_dept_reports
            (model_id, date, failure_rate, item,
             highlight_from, customer, product_number, supplier,
             issue, issue_description, stock, immediately_action, sorting_rework,
             root_cause, short_term_solution, long_term_solution,
             8d_report_received_day, action_lot,
             responsible_person, status,
             user_id, remark, btc_no)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            'isssssssssssssssssssiss',
            $model_id,
            $date,
            $failure_rate,
            $item,
            $highlight_from,
            $customer,
            $product_number,
            $supplier,
            $issue,
            $issue_description,
            $stock,
            $immediately_action,
            $sorting_rework,
            $root_cause,
            $short_term_solution,
            $long_term_solution,
            $report_received_day,
            $action_lot,
            $responsible_person,
            $status,
            $user_id,
            $remark,
            $btc_no
        );

        if ($stmt->execute()) {
            $report_id = $this->conn->insert_id;
            $message = "$user_name just added new SQE report to model $model_name";

            $notifStmt = $this->conn->prepare("
                INSERT INTO notifications (user_id, multi_dept_reports_id, message, model_id, is_read)
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

    public function addPDReports()
    {
        $data = $_POST;

        $model_id       = isset($data['model_id']) ? (int)$data['model_id'] : 0;
        $station_id     = isset($data['station_id']) ? (int)$data['station_id'] : 0;
        $device_id      = isset($data['device_id']) && $data['device_id'] != "0" ? (int)$data['device_id'] : null;
        $shift          = $data['shift'] ?? null;
        $date           = !empty($data['date']) ? date('Y-m-d', strtotime($data['date'])) : null;
        $time_start     = trim($data['time_start']) ?? "";
        $time_finish    = trim($data['time_finish']) ?? "";

        $error_code_id      = isset($data['error_code_id']) ? (int)$data['error_code_id'] : null;
        $root_cause             = $data['root_cause'] ?? null;
        $action_taken             = $data['action_taken'] ?? null;
        $preventive_action             = $data['preventive_action'] ?? null;

        $responsible_person = $data['responsible_person'] ?? null;

        $user_id = isset($data['user_id']) ? (int)$data['user_id'] : 0;
        $remark  = $data['remark'] ?? null;

        if (!$model_id || !$station_id || !$user_id) {
            http_response_code(400);
            echo json_encode(["message" => "All required fields must be filled"]);
            exit();
        }

        // upload photo
        $failure_photo = null;

        if (isset($_FILES['failure_photo']) && $_FILES['failure_photo']['error'] === 0) {
            $uploadDir = __DIR__ . "/../uploads/failure_photos/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . "_" . uniqid() . "." . pathinfo($_FILES['failure_photo']['name'], PATHINFO_EXTENSION);

            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['failure_photo']['tmp_name'], $targetPath)) {
                $failure_photo = "uploads/failure_photos/" . $fileName;
            }
        }

        $improvement_photo = null;

        if (isset($_FILES['improvement_photo']) && $_FILES['improvement_photo']['error'] === 0) {
            $uploadDir = __DIR__ . "/../uploads/failure_photos/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . "_" . uniqid() . "." . pathinfo($_FILES['improvement_photo']['name'], PATHINFO_EXTENSION);

            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['improvement_photo']['tmp_name'], $targetPath)) {
                $improvement_photo = "uploads/failure_photos/" . $fileName;
            }
        }

        $userQuery = $this->conn->query("SELECT name FROM users WHERE id = $user_id");
        $user = $userQuery->fetch_assoc();
        $user_name = $user['name'] ?? 'Unknown User';

        $modelQuery = $this->conn->query("SELECT model_name FROM models WHERE id = $model_id");
        $model = $modelQuery->fetch_assoc();
        $model_name = $model['model_name'] ?? 'Unknown Model';

        $stmt = $this->conn->prepare("
            INSERT INTO multi_dept_reports
            (model_id,
                station_id,
                device_id,
                shift,
                date,
                time_start,
                time_finish,
                error_code_id,
                root_cause,
                action_taken,
                preventive_action,
                responsible_person,
                user_id,
                remark,
                failure_photo,
                improvement_photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            'iiissssissssisss',
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
            $preventive_action,
            $responsible_person,
            $user_id,
            $remark,
            $failure_photo,
            $improvement_photo
        );

        if ($stmt->execute()) {

            $report_id = $this->conn->insert_id;
            $message = "$user_name just added new PD report to model $model_name";

            $notifStmt = $this->conn->prepare("
                INSERT INTO notifications (user_id, multi_dept_reports_id, message, model_id, is_read)
                VALUES (?, ?, ?, ?, 0)
            ");

            $notifStmt->bind_param("iisi", $user_id, $report_id, $message, $model_id);
            $notifStmt->execute();

            echo json_encode([
                "success" => true,
                "message" => "New report added successfully"
            ]);
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
    public function addFEReports()
    {
        $data = $_POST;

        $model_id       = isset($data['model_id']) ? (int)$data['model_id'] : 0;
        $station_id     = isset($data['station_id']) ? (int)$data['station_id'] : 0;
        $device_id      = isset($data['device_id']) && $data['device_id'] != "0" ? (int)$data['device_id'] : null;
        $shift          = $data['shift'] ?? null;
        $date           = !empty($data['date']) ? date('Y-m-d', strtotime($data['date'])) : null;
        $time_start     = trim($data['time_start']) ?? "";
        $time_finish    = trim($data['time_finish']) ?? "";

        $error_code_id      = isset($data['error_code_id']) ? (int)$data['error_code_id'] : null;
        $root_cause             = $data['root_cause'] ?? null;
        $action_taken             = $data['action_taken'] ?? null;
        $preventive_action             = $data['preventive_action'] ?? null;

        $responsible_person = $data['responsible_person'] ?? null;

        $user_id = isset($data['user_id']) ? (int)$data['user_id'] : 0;
        $remark  = $data['remark'] ?? null;

        if (!$model_id || !$station_id || !$user_id) {
            http_response_code(400);
            echo json_encode(["message" => "All required fields must be filled"]);
            exit();
        }

        // upload photo
        $failure_photo = null;

        if (isset($_FILES['failure_photo']) && $_FILES['failure_photo']['error'] === 0) {
            $uploadDir = __DIR__ . "/../uploads/failure_photos/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . "_" . uniqid() . "." . pathinfo($_FILES['failure_photo']['name'], PATHINFO_EXTENSION);

            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['failure_photo']['tmp_name'], $targetPath)) {
                $failure_photo = "uploads/failure_photos/" . $fileName;
            }
        }

        $improvement_photo = null;

        if (isset($_FILES['improvement_photo']) && $_FILES['improvement_photo']['error'] === 0) {
            $uploadDir = __DIR__ . "/../uploads/failure_photos/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . "_" . uniqid() . "." . pathinfo($_FILES['improvement_photo']['name'], PATHINFO_EXTENSION);

            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['improvement_photo']['tmp_name'], $targetPath)) {
                $improvement_photo = "uploads/failure_photos/" . $fileName;
            }
        }

        $userQuery = $this->conn->query("SELECT name FROM users WHERE id = $user_id");
        $user = $userQuery->fetch_assoc();
        $user_name = $user['name'] ?? 'Unknown User';

        $modelQuery = $this->conn->query("SELECT model_name FROM models WHERE id = $model_id");
        $model = $modelQuery->fetch_assoc();
        $model_name = $model['model_name'] ?? 'Unknown Model';

        $stmt = $this->conn->prepare("
            INSERT INTO multi_dept_reports
            (model_id,
                station_id,
                device_id,
                shift,
                date,
                time_start,
                time_finish,
                error_code_id,
                root_cause,
                action_taken,
                preventive_action,
                responsible_person,
                user_id,
                remark,
                failure_photo,
                improvement_photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            'iiissssissssisss',
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
            $preventive_action,
            $responsible_person,
            $user_id,
            $remark,
            $failure_photo,
            $improvement_photo
        );

        if ($stmt->execute()) {

            $report_id = $this->conn->insert_id;
            $message = "$user_name just added new FE/FME report to model $model_name";

            $notifStmt = $this->conn->prepare("
                INSERT INTO notifications (user_id, multi_dept_reports_id, message, model_id, is_read)
                VALUES (?, ?, ?, ?, 0)
            ");

            $notifStmt->bind_param("iisi", $user_id, $report_id, $message, $model_id);
            $notifStmt->execute();

            echo json_encode([
                "success" => true,
                "message" => "New report added successfully"
            ]);
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
    public function addFMEReports()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $model_id       = $data['model_id'] ?? "";
        $station_id     = $data['station_id'] ?? "";
        $device_id = isset($data['device_id']) && $data['device_id'] != "0" ? (int)$data['device_id'] : null;
        $shift          = $data['shift'] ?? "";
        $date           =  !empty($data['date']) ? date('Y-m-d', strtotime($data['date'])) : null;
        $error_code_id  = $data['error_code_id'] ?? "";

        $input_quantity     = $data['input_quantity'] ?? "";
        $defect_quantity    = $data['defect_quantity'] ?? "";
        $failure_rate    = $data['failure_rate'] ?? "";

        $root_cause     = $data['root_cause'] ?? "";
        $short_term_solution  = $data['short_term_solution'] ?? "";
        $long_term_solution  = $data['long_term_solution'] ?? "";

        $responsible_person = $data['responsible_person'] ?? "";
        $status = $data['status'] ?? "";

        $user_id        = $data['user_id'] ?? "";
        $remark         = $data['remark'] ?? "";

        if (
            !$model_id || !$station_id || !$user_id
        ) {
            http_response_code(400);
            echo json_encode(["message" => "All fields are required"]);
            exit();
        }

        $userQuery = $this->conn->query("SELECT name FROM users WHERE id = $user_id");
        $user = $userQuery->fetch_assoc();
        $user_name = $user['name'] ?? 'Unknown User';

        $modelQuery = $this->conn->query("SELECT model_name FROM models WHERE id = $model_id");
        $model = $modelQuery->fetch_assoc();
        $model_name = $model['model_name'] ?? 'Unknown Model';

        // Prepare insert statement
        $stmt = $this->conn->prepare("
            INSERT INTO multi_dept_reports
            (model_id, station_id, device_id, shift, date, input_quantity, defect_quantity, failure_rate,
            error_code_id, root_cause, short_term_solution, long_term_solution, responsible_person, status,
            user_id, remark)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            'iiissiiissssssis',
            $model_id,
            $station_id,
            $device_id,
            $shift,
            $date,
            $input_quantity,
            $defect_quantity,
            $failure_rate,
            $error_code_id,
            $root_cause,
            $short_term_solution,
            $long_term_solution,
            $responsible_person,
            $status,
            $user_id,
            $remark
        );

        if ($stmt->execute()) {
            $report_id = $this->conn->insert_id;
            $message = "$user_name just added new QA report to model $model_name";

            $notifStmt = $this->conn->prepare("
                INSERT INTO notifications (user_id, multi_dept_reports_id, message, model_id, is_read)
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

    public function updateStatus($data)
    {
        $id = (int)($data['id'] ?? 0);
        $status = trim($data['status'] ?? '');

        if (!$id || !$status) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Invalid data"
            ]);
            exit();
        }

        // Ambil status saat ini
        $checkStmt = $this->conn->prepare("
        SELECT status
        FROM multi_dept_reports
        WHERE id = ?
    ");
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Report not found"
            ]);
            exit();
        }

        $current = $result->fetch_assoc();

        // Jika sudah Close, tidak boleh diubah lagi
        if ($current['status'] === 'Close') {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Status is already Close and cannot be changed."
            ]);
            exit();
        }

        // Validasi status yang diperbolehkan
        $allowedStatus = ['Open', 'Close'];

        if (!in_array($status, $allowedStatus, true)) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Invalid status"
            ]);
            exit();
        }

        $stmt = $this->conn->prepare("
        UPDATE multi_dept_reports
        SET status = ?
        WHERE id = ?
    ");

        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Status updated successfully."
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to update status",
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

        // Ambil data report termasuk path foto
        $stmt = $this->conn->prepare("
            SELECT failure_photo, improvement_photo
            FROM multi_dept_reports
            WHERE id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["message" => "Report not found"]);
            exit();
        }

        $report = $result->fetch_assoc();

        // Hapus file jika ada
        if (!empty($report['failure_photo'])) {

            // contoh isi DB:
            // uploads/failure_photos/1751372312_abc.jpg
            $filePath = __DIR__ . "/../" . $report['failure_photo'];

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        if (!empty($report['improvement_photo'])) {

            $filePath = __DIR__ . "/../" . $report['improvement_photo'];

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Hapus notifikasi
        $deleteNotif = $this->conn->prepare("
        DELETE FROM notifications
        WHERE multi_dept_reports_id = ?
    ");
        $deleteNotif->bind_param("i", $id);
        $deleteNotif->execute();

        // Hapus report
        $deleteReport = $this->conn->prepare("
        DELETE FROM multi_dept_reports
        WHERE id = ?
    ");
        $deleteReport->bind_param("i", $id);

        if ($deleteReport->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Report deleted successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to delete report",
                "error" => $deleteReport->error
            ]);
        }
    }
}

// === Auto-handle access file directly ===
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new ReportController($conn);
    $controller->handle($_SERVER['REQUEST_METHOD']);
}
