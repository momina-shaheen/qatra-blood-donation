<?php
require_once "config.php";
require_once "auth.php";
requireLogin();

function renderTable($conn, $title, $sql) {
    $result = $conn->query($sql);
    echo "<h2>$title <span class='count'>(" . $result->num_rows . " rows)</span></h2>";
    if ($result->num_rows === 0) {
        echo "<p class='empty'>No records yet.</p>";
        return;
    }
    echo "<table><thead><tr>";
    $fields = $result->fetch_fields();
    foreach ($fields as $f) echo "<th>" . htmlspecialchars($f->name) . "</th>";
    echo "</tr></thead><tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $val) echo "<td>" . htmlspecialchars($val ?? '') . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Records — Qatra Admin</title>
<style>
  body{font-family:'Segoe UI',Arial,sans-serif;background:#FBF8F4;color:#1A1008;margin:0;padding:2rem;}
  .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;}
  .topbar h1{font-size:1.6rem;margin:0;}
  .topbar a{background:#1A1008;color:white;padding:8px 16px;border-radius:8px;text-decoration:none;font-size:0.85rem;}
  h2{font-size:1.1rem;margin-top:2rem;border-bottom:2px solid #C8102E;padding-bottom:6px;}
  .count{color:#8A7A72;font-size:0.85rem;font-weight:400;}
  table{width:100%;border-collapse:collapse;margin-top:10px;background:white;border-radius:8px;overflow:hidden;font-size:0.85rem;}
  th,td{padding:8px 12px;text-align:left;border-bottom:1px solid #eee;}
  th{background:#1A1008;color:white;}
  tr:hover{background:#FFF5F5;}
  .empty{color:#aaa;font-size:0.85rem;}
</style>
</head>
<body>
  <div class="topbar">
    <h1>📋 Database Records <span style="font-size:0.85rem;color:#8A7A72;">— logged in as <?= htmlspecialchars($_SESSION['admin_username']) ?></span></h1>
    <a href="logout.php">Logout</a>
  </div>

  <?php
  renderTable($conn, "Donors", "SELECT id, name, blood_group, city, area, phone, age, donations, available, last_donation, created_at FROM donors ORDER BY id DESC");
  renderTable($conn, "Hospitals", "SELECT id, name, city, type, phone, verified, created_at FROM hospitals ORDER BY id DESC");
  renderTable($conn, "Hospital Inventory", "SELECT id, hospital_id, blood_group, units FROM hospital_inventory ORDER BY hospital_id");
  renderTable($conn, "Blood Requests", "SELECT id, patient, blood_group, location, hospital, units, urgent, created_at FROM blood_requests ORDER BY id DESC");
  renderTable($conn, "Blood Camps", "SELECT id, org, city, camp_date, camp_time, location, target_donors, icon, created_at FROM camps ORDER BY camp_date");
  ?>
</body>
</html>
