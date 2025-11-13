<?php
session_start();
include 'db_connect.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id,name,email,role,created_at FROM users WHERE id = ?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body><div class="container mt-4">
<div class="card p-3 col-md-6 offset-md-3">
<h4>Profile</h4>
<p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
<p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
<p><a class="btn btn-link" href="marketplace.php">Go to Marketplace</a></p>
</div></div></body></html>
