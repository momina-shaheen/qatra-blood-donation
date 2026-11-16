<?php
require_once "config.php";
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$method = $_SERVER['REQUEST_METHOD'];

// ============ GET: return all donors ============
if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM donors ORDER BY created_at DESC");
    $donors = [];
    while ($row = $result->fetch_assoc()) {
        $donors[] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "blood" => $row['blood_group'],
            "city" => $row['city'],
            "area" => $row['area'] ?: 'N/A',
            "phone" => $row['phone'],
            "age" => $row['age'] ?? 0,
            "donations" => (int)$row['donations'],
            "available" => (bool)$row['available'],
            "lastDon" => $row['last_donation']
        ];
    }
    echo json_encode($donors);
    exit;
}

// ============ POST: register a new donor ============
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $name = $conn->real_escape_string($data['name'] ?? '');
    $blood = $conn->real_escape_string($data['blood'] ?? '');
    $city = $conn->real_escape_string($data['city'] ?? '');
    $area = $conn->real_escape_string($data['area'] ?? '');
    $phone = $conn->real_escape_string($data['phone'] ?? '');
    $age = null;
    if (!empty($data['dob'])) {
        $dob = new DateTime($data['dob']);
        $age = (new DateTime())->diff($dob)->y;
    }

    if (!$name || !$blood || !$city || !$phone) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields (name, blood, city, phone)"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO donors (name, blood_group, city, area, phone, age, donations, available) VALUES (?, ?, ?, ?, ?, ?, 0, 1)");
    $stmt->bind_param("sssssi", $name, $blood, $city, $area, $phone, $age);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "id" => $conn->insert_id, "message" => "Donor registered successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to register donor: " . $stmt->error]);
    }
    exit;
}

http_response_code(405);
echo json_encode(["error" => "Method not allowed"]);
?>
