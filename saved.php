<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: pinbored.php"); // redirect if not logged in
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Fetch saved pins
$sql = "SELECT p.*
        FROM saves s
        JOIN pins p ON s.pin_id = p.id
        WHERE s.user_id = ?
        ORDER BY s.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pins = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Saved Pins</title>
  <link rel="stylesheet" href="home.css"> <!-- reuse same styling -->
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <a href="pinboard.php"><img src="image/logo.jpg" class="logo"></a>
    <a href="home.php" class="icon">🏠</a>
    <a href="upload.php" class="icon">➕</a>
    <div class="bottom-icons">
      <a href="saved.php" class="icon" style="color:red;">💾</a>
      <a href="profile.php" class="icon">👤</a>
    </div>
  </div>

  <!-- Topbar -->
  <div class="topbar">
    <form class="search-form" method="get" action="search.php">
      <input type="text" name="q" placeholder="Search saved pins...">
      <button type="submit">Search</button>
    </form>

    <div style="position:absolute; right:20px;">
      <button onclick="toggleDropdown()" class="profile-btn">☰</button>
      <div id="profileDropdown" class="profile-dropdown">
        <a href="profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </div>

  <!-- Pins -->
  <div class="pin-container">
    <?php if ($pins->num_rows > 0): ?>
      <?php while($pin = $pins->fetch_assoc()): ?>
        <div class="pin">
          <a href="pin.php?id=<?php echo $pin['id']; ?>">
            <img src="<?php echo htmlspecialchars($pin['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($pin['title']); ?>">
          </a>
          <div class="overlay">
            <a href="save.php?id=<?php echo $pin['id']; ?>" class="save-btn">Unsave</a>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="margin-left:90px;">You haven’t saved any pins yet.</p>
    <?php endif; ?>
  </div>

<script>
function toggleDropdown() {
  document.getElementById("profileDropdown").classList.toggle("show");
}
</script>

</body>
</html>
