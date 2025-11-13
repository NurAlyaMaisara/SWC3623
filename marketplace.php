<?php
session_start();
include 'db_connect.php';
?>

<!doctype html>
<html><head><meta charset="utf-8"><title>Marketplace</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet"></head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark"><div class="container">
<a class="navbar-brand" href="index.php">
<img src="assets/images/P.png" alt="PeerSquare" style="height:45px;"></a>
  <div class="ms-auto">
    <?php if(isset($_SESSION['user_id'])): ?>
      <a class="btn btn-outline-light btn-sm" href="sell.php">Sell</a>
      <a class="btn btn-danger btn-sm" href="logout.php">Logout</a>
    <?php else: ?>
      <a class="btn btn-outline-light btn-sm" href="login.php">Login</a>
    <?php endif; ?>
  </div>
</div>
</nav>

<div class="container mt-4">
  <h3>Marketplace</h3>
  <div class="row">
    <?php
    $stmt = $conn->prepare("SELECT m.*, u.name FROM marketplace m LEFT JOIN users u ON m.user_id = u.id WHERE m.status='approved' ORDER BY m.created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()):
    ?>
<div class="col-md-4 mb-3">
  <div class="marketplace-card card p-2">
    <?php if($row['image']): ?>
      <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="product mb-2">
    <?php endif; ?>
    <h5><?php echo htmlspecialchars($row['title']); ?></h5>
    <p class="price">RM <?php echo number_format($row['price'],2); ?></p>
    <p class="description"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
    <p class="small">Seller: <?php echo htmlspecialchars($row['name']); ?></p>
  </div>
</div>

</div>
    <?php endwhile; $stmt->close(); ?>
  </div>
</div>
</body></html>
