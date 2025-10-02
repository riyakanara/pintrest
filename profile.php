<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: pinbored.php");
  exit;
}

$user_id = (int)$_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

// Created Pins
$createdPins = $conn->query("
  SELECT pins.*, users.username 
  FROM pins 
  JOIN users ON pins.user_id = users.id
  WHERE pins.user_id=$user_id
  ORDER BY pins.created_at DESC
");

// Saved Pins
$savedPins = $conn->query("
  SELECT pins.*, users.username 
  FROM saves 
  JOIN pins ON saves.pin_id = pins.id 
  JOIN users ON pins.user_id = users.id
  WHERE saves.user_id=$user_id
  ORDER BY saves.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($user['username']); ?> - Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="profile.css">
</head>
<body>
  <div class="profile-header">
    <img src="<?php echo $user['profile_pic'] ?: 'image/logo.jpg'; ?>" alt="Profile">
    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
    <p><?php echo htmlspecialchars($user['email']); ?></p>
  </div>

  <!-- Tabs -->
  <div class="tabs">
    <button class="tab-btn active" data-tab="created">Created</button>
    <button class="tab-btn" data-tab="saved">Saved</button>
  </div>

  <!-- Created Pins -->
  <div id="created" class="tab-content active">
    <div class="pin-grid">
      <?php if ($createdPins->num_rows > 0): ?>
        <?php while ($pin = $createdPins->fetch_assoc()): ?>
          <a href="pin.php?id=<?php echo (int)$pin['id']; ?>">
            <div class="pin-card">
              <img src="<?php echo htmlspecialchars($pin['image_url']); ?>" alt="<?php echo htmlspecialchars($pin['title']); ?>">
              <div class="overlay">
                <?php echo htmlspecialchars($pin['title']); ?>
              </div>
            </div>
          </a>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="empty">No pins created yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Saved Pins -->
  <div id="saved" class="tab-content">
    <div class="pin-grid">
      <?php if ($savedPins->num_rows > 0): ?>
        <?php while ($pin = $savedPins->fetch_assoc()): ?>
          <a href="pin.php?id=<?php echo (int)$pin['id']; ?>">
            <div class="pin-card">
              <img src="<?php echo htmlspecialchars($pin['image_url']); ?>" alt="<?php echo htmlspecialchars($pin['title']); ?>">
              <div class="overlay">
                <?php echo htmlspecialchars($pin['title']); ?> <br>
                <span>by <?php echo htmlspecialchars($pin['username']); ?></span>
              </div>
            </div>
          </a>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="empty">No saved pins yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <script>
    // Tab switching
    const tabs = document.querySelectorAll('.tab-btn');
    const contents = document.querySelectorAll('.tab-content');
    tabs.forEach(t => t.addEventListener('click', () => {
      tabs.forEach(b => b.classList.remove('active'));
      contents.forEach(c => c.classList.remove('active'));
      t.classList.add('active');
      document.getElementById(t.dataset.tab).classList.add('active');
    }));
  </script>
</body>
</html>
