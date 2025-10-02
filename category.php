<?php
session_start();
include 'header.php';
include 'db.php';

$cat = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$title = $cat ? htmlspecialchars($cat) : 'All';

$stmt = $conn->prepare("SELECT * FROM pins WHERE category = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $cat);
$stmt->execute();
$pins = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $title; ?> | Category</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="explore.css">
</head>
<body>

<section class="hero" style="background:linear-gradient(135deg,#e4f7ff,#f2ffe8)">
  <h1><?php echo $title; ?> ✨</h1>
  <p>Explore more in “<?php echo $title; ?>”.</p>
</section>

<section class="feed">
  <h2><?php echo $title; ?> Pins</h2>
  <div class="masonry">
    <?php while ($pin = $pins->fetch_assoc()): ?>
      <div class="pin-card">
        <a href="pin.php?id=<?php echo (int)$pin['id']; ?>">
          <img src="<?php echo htmlspecialchars($pin['image_url']); ?>" alt="<?php echo htmlspecialchars($pin['title']); ?>">
        </a>
        <div class="pin-actions">
          <button class="share" title="Share" data-link="pin.php?id=<?php echo (int)$pin['id']; ?>">🔗</button>
          <div class="more">⋮
            <div class="dropdown">
              <?php if (isset($_SESSION['user_id'])): ?>
                <a href="save.php?id=<?php echo (int)$pin['id']; ?>">Save</a>
                <a href="download.php?id=<?php echo (int)$pin['id']; ?>">Download</a>
              <?php else: ?>
                <a href="pinbored.php">Login to Save</a>
                <a href="pinbored.php">Login to Download</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<script>
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('share')) {
      const rel = e.target.getAttribute('data-link');
      const url = (location.origin ? location.origin : window.location.protocol + '//' + window.location.host) + '/' + rel;
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(() => alert('Link copied!'));
      } else {
        prompt('Copy this link:', url);
      }
    }
  });
</script>
</body>
</html>
