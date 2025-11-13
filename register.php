<?php
session_start();
include 'db_connect.php';

$errors = [];
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";
    if(strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";

    if(empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name,email,password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hash);
        if($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['role'] = 'user';
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "An account with that email may already exist.";
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Register | PeerSquare</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-5">
  <div class="col-md-6 offset-md-3 card p-4">
    <h4>Create account</h4>
    <?php if($errors): foreach($errors as $e): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($e); ?></div>
    <?php endforeach; endif; ?>
    <form method="post" action="register.php">
      <input class="form-control mb-2" name="name" placeholder="Full name" required>
      <input class="form-control mb-2" name="email" type="email" placeholder="Email" required>
      <input class="form-control mb-2" name="password" type="password" placeholder="Password" required>
      <button class="btn btn-primary w-100">Register</button>
    </form>
    <p class="mt-2 small">Already have an account? <a href="login.php">Login</a></p>
  </div>
</div>
</body>
</html>
