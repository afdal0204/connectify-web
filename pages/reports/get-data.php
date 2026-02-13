<?php
include './../../config.php'; 
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action == 'getStations' && !empty($_POST['model_id'])) {
    $model_id = $_POST['model_id'];
    $sql = "SELECT id, station_name FROM stations WHERE model_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $model_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

if ($action == 'getDevices' && !empty($_POST['station_id'])) {
    $station_id = $_POST['station_id'];
    $sql = "SELECT id, device_name FROM devices WHERE station_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $station_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

if ($action == 'getSymptom' && !empty($_POST['error_code'])) {
    $error_code = $_POST['error_code'];
    $sql = "SELECT id, symptom FROM error_code WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $error_code);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $symptom = $row ? $row['symptom'] : '';
    echo json_encode(['symptom' => $symptom]);
    exit;
}

if ($action == 'getLine' && !empty($_POST['model_id'])) {
    $model_id = $_POST['model_id'];
    $sql = "SELECT id, line_area, output_target FROM models WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $model_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    echo json_encode([
        'success' => true,
        'model_id' => $model_id,
        'data' => $row
    ]);
    exit;
}

if ($action == 'getModels') {
    $dept_id = $_POST['dept_id'] ?? '';

    if (!empty($dept_id)) {
        // filter berdasarkan department tertentu
        $sql = "SELECT m.id, m.model_name 
                FROM models m
                LEFT JOIN users u_owner ON m.owner_id = u_owner.id
                LEFT JOIN department d ON u_owner.department_id = d.id
                WHERE d.id = ?
                ORDER BY m.model_name ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $dept_id);
    } else {
        // kalau dept ALL, ambil semua model
        $sql = "SELECT id, model_name FROM models ORDER BY model_name ASC";
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
    exit;
}

echo json_encode([]);
exit;
