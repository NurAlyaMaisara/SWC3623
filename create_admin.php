<?php
// create_admin.php - run once to create admin user, then delete for security
include 'db_connect.php';

$name = "Alya";
$email = "admin@peersquare.test";
$pass = "admin123"; // change after creating
$hash = password_hash($pass, PASSWORD_DEFAULT);

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s",$email);
$stmt->execute();
$r = $stmt->get_result();
if($r->num_rows == 0){
  $stmt2 = $conn->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?, 'admin')");
  $stmt2->bind_param("sss", $name, $email, $hash);
  $stmt2->execute();
  echo "Admin created: $email / $pass. Delete this file now!";
  $stmt2->close();
} else {
  echo "Admin already exists.";
}
$stmt->close();
$conn->close();
?>
