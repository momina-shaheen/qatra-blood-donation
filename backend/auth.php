<?php
// Include this AFTER config.php (which already calls session_start()).
// Use requireLogin() at the top of any page that should be admin-only.

function requireLogin() {
    if (empty($_SESSION['admin_id'])) {
        header("Location: login.php");
        exit;
    }
}

function isLoggedIn() {
    return !empty($_SESSION['admin_id']);
}
?>
