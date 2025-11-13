<?php
session_start();
include 'db_connect.php';
if(!isset($_SESSION['user_id'])){
  header("Location: login.php");
  exit;
}
$success = $error = "";
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $user_id = $_SESSION['user_id'];
    $imageName = null;

    // Handle upload if any
    if(isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE){
        $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
        if(in_array($_FILES['image']['type'],$allowed)){
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = uniqid('p_').'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__.'/uploads/'.$imageName);
        } else {
            $error = "Image type not allowed.";
        }
    }

    if(!$error){
        $stmt = $conn->prepare("INSERT INTO marketplace (title,description,price,image,user_id) VALUES (?,?,?,?,?)");
        $stmt->bind_param("ssdsi", $title, $description, $price, $imageName, $user_id);
        if($stmt->execute()){
            $success = "Listing created and awaiting admin approval.";
        } else {
            $error = "Failed to post listing.";
        }
        $stmt->close();
    }
}
?>

<!doctype html>
<html><head><meta charset="utf-8"><title>Sell an item</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<link href="assets/css/style.css" rel="stylesheet">
<body class="bg-light">
<div class="container mt-4">
  <div class="col-md-8 offset-md-2 card p-4">
    <h4>Sell an item</h4>
    <?php if($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <?php if($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post" action="sell.php" enctype="multipart/form-data">
      <input class="form-control mb-2" name="title" placeholder="Title" required>
      <textarea class="form-control mb-2" name="description" placeholder="Description"></textarea>
      <input class="form-control mb-2" name="price" placeholder="Price (RM)" type="number" step="0.01" required>
      <input class="form-control mb-2" name="image" type="file" accept="image/*">
      <button class="btn btn-primary">Post for approval</button>
    </form>
  </div>
</div>
</body></html>
