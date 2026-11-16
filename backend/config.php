<?php
// Start session for login/auth (must be the very first thing, before any output)
session_start();

// Database connection settings — XAMPP defaults are username "root", password "" (blank)
// Change these only if your MySQL setup is different.

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "qatra_db";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
