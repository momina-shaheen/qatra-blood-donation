<?php
require_once "config.php";
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$method = $_SERVER['REQUEST_METHOD'];

// ============ GET: return all hospitals with their inventory ============
if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM hospitals ORDER BY created_at DESC");
    $hospitals = [];
    while ($row = $result->fetch_assoc()) {
        $invResult = $conn->query("SELECT blood_group, units FROM hospital_inventory WHERE hospital_id = " . (int)$row['id']);
        $inv = [];
        while ($invRow = $invResult->fetch_assoc()) {
            $inv[$invRow['blood_group']] = (int)$invRow['units'];
        }
        $hospitals[] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "city" => $row['city'],
            "type" => $row['type'],
            "phone" => $row['phone'],
            "verified" => (bool)$row['verified'],
            "inv" => $inv
        ];
    }
    echo json_encode($hospitals);
    exit;
}

// ============ POST: register a new hospital ============
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $name = $conn->real_escape_string($data['name'] ?? '');
    $city = $conn->real_escape_string($data['city'] ?? '');
    $type = $conn->real_escape_string($data['type'] ?? '');
    $phone = $conn->real_escape_string($data['phone'] ?? '');

    if (!$name || !$city || !$type || !$phone) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields (name, city, type, phone)"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO hospitals (name, city, type, phone, verified) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("ssss", $name, $city, $type, $phone);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "id" => $conn->insert_id, "message" => "Hospital registered, pending verification"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to register hospital: " . $stmt->error]);
    }
    exit;
}

http_response_code(405);
echo json_encode(["error" => "Method not allowed"]);
?>
