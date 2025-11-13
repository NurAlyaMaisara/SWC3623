<?php
session_start();
include 'db_connect.php';

$ok = $err = "";

// Only handle POST if user is logged in
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])){
    $item = trim($_POST['item_name']);
    $cat = trim($_POST['category']);
    $desc = trim($_POST['description']);
    $contact = trim($_POST['contact']);
    $user_id = $_SESSION['user_id'];
    $status = ($_POST['status'] === 'found') ? 'found' : 'lost';

    // Image upload
    $image_name = "";
    if(!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if(!is_dir($target_dir)) mkdir($target_dir, 0755);
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ["jpg","jpeg","png","gif"];
        if(!in_array($imageFileType, $allowed)) {
            $err = "Only JPG, PNG, and GIF files are allowed.";
        } elseif(!getimagesize($_FILES['image']['tmp_name'])) {
            $err = "Uploaded file is not a valid image.";
        } else {
            if(!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $err = "Failed to upload image.";
            }
        }
    }

    if(!$err) {
        $stmt = $conn->prepare("INSERT INTO lostfound (item_name, category, description, status, contact, user_id, image, created_at) VALUES (?,?,?,?,?,?,?,NOW())");
        $stmt->bind_param("sssssis", $item, $cat, $desc, $status, $contact, $user_id, $image_name);
        if($stmt->execute()) $ok = "Reported $status item successfully.";
        else $err = "Failed to report item.";
        $stmt->close();
    }
}

// Fetch all lost & found items
$stmt = $conn->prepare("SELECT l.*, u.name FROM lostfound l LEFT JOIN users u ON l.user_id=u.id ORDER BY l.created_at DESC");
$stmt->execute();
$res = $stmt->get_result();
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Lost & Found</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

  <!-- Header + Report Button or Login Message -->
  <?php if(isset($_SESSION['user_id'])): ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Lost & Found</h3>
      <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#reportForm">Report Item</button>
  </div>

  <!-- Collapsible report form -->
  <div class="collapse mb-4" id="reportForm">
      <div class="card p-3">
          <h5>Report Lost or Found Item</h5>
          <?php if($ok): ?><div class="alert alert-success"><?= htmlspecialchars($ok) ?></div><?php endif;?>
          <?php if($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif;?>
          <form method="post" enctype="multipart/form-data">
              <div class="mb-2">
                  <label>Status</label>
                  <select name="status" class="form-control">
                      <option value="lost">Lost</option>
                      <option value="found">Found</option>
                  </select>
              </div>
              <input class="form-control mb-2" name="item_name" placeholder="Item name" required>
              <input class="form-control mb-2" name="category" placeholder="Category">
              <textarea class="form-control mb-2" name="description" placeholder="Description"></textarea>
              <input class="form-control mb-2" name="contact" placeholder="Contact info" required>
              <label class="form-label">Upload Image (optional)</label>
              <input type="file" class="form-control mb-3" name="image">
              <button class="btn btn-success">Submit Report</button>
          </form>
      </div>
  </div>

  <?php else: ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Lost & Found</h3>
  </div>
  <p class="text-muted mb-3">Please <a href="login.php">log in</a> to report a lost or found item.</p>
  <?php endif; ?>

  <!-- Lost & Found items list -->
  <div class="row">
    <?php while($row = $res->fetch_assoc()): ?>
      <div class="col-md-6 mb-3">
        <div class="card p-2">
          <?php if(!empty($row['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="img-fluid mb-2" alt="Item">
          <?php endif; ?>
          <strong><?= htmlspecialchars($row['item_name']) ?></strong>
          <p class="small text-muted"><?= htmlspecialchars($row['category']) ?> — <?= htmlspecialchars($row['status']) ?></p>
          <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
          <p class="small">Contact: <?= htmlspecialchars($row['contact']) ?> (Reported by <?= htmlspecialchars($row['name']) ?>)</p>
        </div>
      </div>
    <?php endwhile; $stmt->close(); ?>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
