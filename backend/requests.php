<?php
require_once "config.php";
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$method = $_SERVER['REQUEST_METHOD'];

// ============ GET: return all blood requests ============
if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM blood_requests ORDER BY created_at DESC");
    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = [
            "id" => $row['id'],
            "patient" => $row['patient'],
            "blood" => $row['blood_group'],
            "location" => $row['location'],
            "hospital" => $row['hospital'],
            "units" => (int)$row['units'],
            "urgent" => (bool)$row['urgent'],
            "time" => $row['created_at']
        ];
    }
    echo json_encode($requests);
    exit;
}

// ============ POST: create a new blood request ("Need Blood?" form) ============
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $patient = $conn->real_escape_string($data['patient'] ?? '');
    $blood = $conn->real_escape_string($data['blood'] ?? '');
    $location = $conn->real_escape_string($data['location'] ?? '');
    $hospital = $conn->real_escape_string($data['hospital'] ?? '');
    $units = (int)($data['units'] ?? 1);
    $urgent = !empty($data['urgent']) ? 1 : 0;

    if (!$patient || !$blood || !$location || !$hospital) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields (patient, blood, location, hospital)"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO blood_requests (patient, blood_group, location, hospital, units, urgent) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $patient, $blood, $location, $hospital, $units, $urgent);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "id" => $conn->insert_id, "message" => "Blood request posted"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to post request: " . $stmt->error]);
    }
    exit;
}

http_response_code(405);
echo json_encode(["error" => "Method not allowed"]);
?>
