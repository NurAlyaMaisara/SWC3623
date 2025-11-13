<?php
session_start();
include 'db_connect.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>PeerSquare</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- ===== NAVBAR (Desktop Style) ===== -->
<nav class="navbar navbar-dark">
  <div class="container d-flex justify-content-between align-items-center">

    <!-- LOGO -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="assets/images/P.png" alt="PeerSquare" style="height:45px; margin-right:8px;">
    </a>

    <!-- QUICK LINKS -->
    <div class="d-flex align-items-center">
      <a href="marketplace.php" class="nav-link px-3 text-white">Marketplace</a>
      <a href="sell.php" class="nav-link px-3 text-white">Sell</a>
      <a href="lostfound.php" class="nav-link px-3 text-white">Lost & Found</a>
    </div>

    <!-- USER / ADMIN BUTTONS -->
    <div class="d-flex align-items-center">
      <?php if(isset($_SESSION['user_id'])): ?>
        <?php if($_SESSION['role'] == 'admin'): ?>
          <a class="btn btn-warning btn-sm mx-1" href="admin/dashboard.php">Admin</a>
        <?php endif; ?>
        <a class="btn btn-outline-light btn-sm mx-1" href="profile.php">Profile</a>
        <a class="btn btn-danger btn-sm mx-1" href="logout.php">Logout</a>
      <?php else: ?>
        <a class="btn btn-outline-light btn-sm mx-1" href="login.php">Login</a>
        <a class="btn btn-primary btn-sm mx-1" href="register.php">Register</a>
      <?php endif; ?>
    </div>

  </div>
</nav>
<!-- ===== END NAVBAR ===== -->


<!-- ===== PAGE CONTENT ===== -->
<div class="container mt-4">
  <div class="row">
    <div class="col-md-12">
      <h3>Latest Marketplace</h3>
      <div class="row">
        <?php
        $stmt = $conn->prepare("SELECT m.id, m.title, m.price, m.image, m.status, u.name FROM marketplace m LEFT JOIN users u ON m.user_id = u.id WHERE m.status='approved' ORDER BY m.created_at DESC LIMIT 12");
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()):
        ?>
        <div class="col-md-3 mb-3 fade-in">
          <div class="card p-2">
            <?php if($row['image']): ?>
              <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="product mb-2" alt="">
            <?php endif; ?>
            <h5><?php echo htmlspecialchars($row['title']); ?></h5>
            <p class="small text-muted">By <?php echo htmlspecialchars($row['name']); ?> — RM <?php echo number_format($row['price'],2); ?></p>
            <a href="marketplace.php" class="btn btn-sm btn-outline-primary">View</a>
          </div>
        </div>
        <?php endwhile; $stmt->close(); ?>
      </div>
    </div>
  </div>
</div>

<div class="footer">PeerSquare &mdash; Student Marketplace</div>
</body>
</html>
