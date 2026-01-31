<?php
include './../../config.php'; 
header('Content-Type: application/json');

if (!empty($_POST['model_id'])) {$model_id = $_POST['model_id'];

    $sql = "SELECT id, station_name FROM stations WHERE model_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $model_id);
    $stmt->execute();
    $res = $stmt->get_result();

    $stations = [];
    while ($row = $res->fetch_assoc()) {
        $stations[] = ['id' => $row['id'], 'station_name' => $row['station_name']];
    }

    echo json_encode(['stations' => $stations]);
    exit;
}
?>
