<?php
session_start();
include 'header.php';
include 'db.php';

// trending pins
$pins = $conn->query("SELECT * FROM pins ORDER BY created_at DESC LIMIT 20");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Explore | Pinterest Clone</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="explore.css">
</head>
<body>

  <!-- Hero / Inspiration -->
  <section class="hero">
    <h1>Discover ideas for every moment ✨</h1>
    <p>"Pinterest is not just pictures, it's the start of your next great idea."</p>
  </section>

  <!-- Categories (unchanged layout, image tiles click to category.php) -->
  <section class="categories">
    <h2>Explore Categories</h2>
    <div class="grid">
      <a href="category.php?cat=Food" class="card">
        <img src="image/1.jpg" alt="Food"><span>Food & Recipes</span>
      </a>
      <a href="category.php?cat=Art" class="card">
        <img src="image/4.jpg" alt="Art"><span>Art & Creativity</span>
      </a>
      <a href="category.php?cat=Quotes" class="card">
        <img src="image/7.jpg" alt="Quotes"><span>Quotes</span>
      </a>
      <a href="category.php?cat=Fashion" class="card">
        <img src="image/20.jpg" alt="Fashion"><span>Fashion</span>
      </a>
      <a href="category.php?cat=HomeDecor" class="card">
        <img src="image/21.jpg" alt="Home Decor"><span>Home Decor</span>
      </a>
      <a href="category.php?cat=Photography" class="card">
        <img src="image/13.jpg" alt="Photography"><span>Photography</span>
      </a>
      <a href="category.php?cat=Festivals" class="card">
        <img src="image/14.jpg" alt="Festivals"><span>Festivals</span>
      </a>
      <a href="category.php?cat=Beauty" class="card">
        <img src="image/23.jpg" alt="Beauty"><span>Beauty</span>
      </a>
      <a href="category.php?cat=Culture" class="card">
        <img src="image/11.jpg" alt="Culture"><span>Culture</span>
      </a>
    </div>
  </section>

  <!-- Decorative Section -->
  <section class="decor">
    <h2>✨ Inspiration for You</h2>
    <p>"Ideas are the beginning of all journeys. Let Pinterest inspire your next step."</p>
    <div class="quote-grid">
      <div class="quote">"Creativity is intelligence having fun."</div>
      <div class="quote">"Small ideas can spark big changes."</div>
      <div class="quote">"Your dream home starts with a pin."</div>
      <div class="quote">"A more inspired internet, a better world."</div>
    </div>
  </section>

  <!-- Trending Now (dynamic, same visual format, with hover actions) -->
  <section class="feed">
    <h2>Trending Now 🔥</h2>
    <div class="masonry">
      <?php while ($pin = $pins->fetch_assoc()): ?>
        <div class="pin-card">
          <a href="pin.php?id=<?php echo (int)$pin['id']; ?>">
            <img src="<?php echo htmlspecialchars($pin['image_url']); ?>" alt="<?php echo htmlspecialchars($pin['title']); ?>">
          </a>

          <!-- Hover icons -->
          <div class="pin-actions">
            <button class="share" title="copy" data-link="pin.php?id=<?php echo (int)$pin['id']; ?>">🔗</button>
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

  <footer>
    <p>© 2025 Pinterest Clone | Made with ❤️ for creativity</p>
  </footer>

  <script>
    // Share: copy link to clipboard
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
