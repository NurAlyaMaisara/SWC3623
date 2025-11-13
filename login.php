<?php
session_start();
include 'db_connect.php';
$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $res = $stmt->get_result();
    if($user = $res->fetch_assoc()){
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            // prefer admin dashboard if admin
            if($user['role'] === 'admin'){
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else $error = "Invalid credentials";
    } else $error = "Invalid credentials";
    $stmt->close();
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-5">
  <div class="col-md-4 offset-md-4 card p-4">
    <h4>Login</h4>
    <?php if($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post" action="login.php">
      <input class="form-control mb-2" name="email" type="email" placeholder="Email" required>
      <input class="form-control mb-2" name="password" type="password" placeholder="Password" required>
      <button class="btn btn-primary w-100">Login</button>
    </form>
    <p class="mt-2 small">No account? <a href="register.php">Register</a></p>
  </div>
</div>
</body></html>
