<?php
require_once "config.php";
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$method = $_SERVER['REQUEST_METHOD'];

// ============ GET: return all camps ============
if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM camps ORDER BY camp_date ASC");
    $camps = [];
    while ($row = $result->fetch_assoc()) {
        $camps[] = [
            "id" => $row['id'],
            "org" => $row['org'],
            "city" => $row['city'],
            "date" => $row['camp_date'],
            "time" => $row['camp_time'],
            "location" => $row['location'],
            "target" => (int)$row['target_donors'],
            "icon" => $row['icon']
        ];
    }
    echo json_encode($camps);
    exit;
}

// ============ POST: add a new camp (admin) ============
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $org = $conn->real_escape_string($data['org'] ?? '');
    $city = $conn->real_escape_string($data['city'] ?? '');
    $date = $conn->real_escape_string($data['date'] ?? '');
    $time = $conn->real_escape_string($data['time'] ?? '');
    $location = $conn->real_escape_string($data['location'] ?? '');
    $target = (int)($data['target'] ?? 0);
    $icon = $conn->real_escape_string($data['icon'] ?? '🩸');

    if (!$org || !$city || !$date) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields (org, city, date)"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO camps (org, city, camp_date, camp_time, location, target_donors, icon) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $org, $city, $date, $time, $location, $target, $icon);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "id" => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to add camp: " . $stmt->error]);
    }
    exit;
}

http_response_code(405);
echo json_encode(["error" => "Method not allowed"]);
?>
