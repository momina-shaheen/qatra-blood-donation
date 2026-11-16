<?php
require_once "config.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session ID on login to prevent session fixation attacks
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        header("Location: records.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login — Qatra</title>
<style>
  body{font-family:'Segoe UI',Arial,sans-serif;background:#1A1008;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}
  .login-box{background:#FBF8F4;border-radius:16px;padding:2.5rem;width:340px;box-shadow:0 20px 60px rgba(0,0,0,0.3);}
  .login-box h1{font-size:1.4rem;margin:0 0 0.3rem;color:#1A1008;}
  .login-box p{font-size:0.85rem;color:#8A7A72;margin:0 0 1.5rem;}
  .login-box input{width:100%;padding:10px 12px;margin-bottom:12px;border:1px solid #ddd;border-radius:8px;font-size:0.9rem;box-sizing:border-box;}
  .login-box button{width:100%;padding:11px;background:#C8102E;color:white;border:none;border-radius:8px;font-weight:700;font-size:0.95rem;cursor:pointer;}
  .login-box button:hover{background:#A50D26;}
  .error{background:#FEE2E2;color:#B91C1C;padding:10px 12px;border-radius:8px;font-size:0.85rem;margin-bottom:12px;}
  .hint{font-size:0.75rem;color:#aaa;margin-top:1rem;text-align:center;}
</style>
</head>
<body>
  <div class="login-box">
    <h1>🩸 Qatra Admin</h1>
    <p>Sign in to view registered records</p>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login →</button>
    </form>
    <div class="hint">Default: admin / admin123</div>
  </div>
</body>
</html>
