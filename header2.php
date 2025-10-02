<?php
// header2.php
include_once 'config.php'; // Ensure you have 'config.php' or 'db.php' for database connection
// Check if a session has not been started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$loggedInUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

// current user
$currentUser = null;
if ($loggedInUserId && isset($conn)) { // Check for $conn existence
    $stmt = $conn->prepare("SELECT id, username, email, profile_pic FROM users WHERE id=?");
    $stmt->bind_param("i", $loggedInUserId);
    $stmt->execute();
    $currentUser = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// accounts for dropdown
$accounts = [];
if (isset($conn)) { // Check for $conn existence
    $accRes = $conn->query("SELECT id, username, email, profile_pic FROM users ORDER BY username ASC");
    while ($r = $accRes->fetch_assoc()) $accounts[] = $r;
}

// FIX: Define $searchTerm to prevent PHP notice if not set in calling script
$searchTerm = $searchTerm ?? ''; 
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Pinterest Clone</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <aside class="sidebar">
        <a class="logo-link" href="pinboard.php"><img src="image/logo.jpg" alt="Logo" class="sidebar-logo"></a>
        <nav class="side-icons">
            <a href="home.php" data-tooltip="Home" class="side-btn"><i class="fa-solid fa-house-chimney"></i></a>
            <a href="upload.php" data-tooltip="Create" class="side-btn"><i class="fa-solid fa-plus"></i></a>
            <a href="intrest.php" data-tooltip="Add Interest" class="side-btn"><i class="fa-solid fa-star"></i></a>
            <a href="messages.php" data-tooltip="Messages" class="side-btn"><i class="fa-solid fa-comment-dots"></i></a>
        </nav>
        <div class="sidebar-bottom">
            <a href="#" class="side-btn profile-toggle" id="user-profile">
                <?php if (!empty($currentUser['profile_pic'])): ?>
                    <img src="<?php echo htmlspecialchars($currentUser['profile_pic']); ?>" class="side-avatar" alt="Profile">
                <?php else: ?>
                    <i class="fa-solid fa-user"></i>
                <?php endif; ?>
            </a>
        </div>
    </aside>

    <header class="topbar">
        <form class="search-form" method="get" action="home.php">
            <input type="text" name="q" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search for pins...">
            <button type="submit" class="btn-search">Search</button>
        </form>

        <div class="topbar-profile">
             <img src="<?php echo htmlspecialchars($currentUser['profile_pic'] ?? 'image/logo.jpg'); ?>" class="top-profile-pic" id="topProfilePic">
            
            <div class="profile-dropdown" id="account-slider">
                <?php if ($currentUser): ?>
                    <div class="profile-section">
                        <img src="<?php echo htmlspecialchars($currentUser['profile_pic'] ?? 'image/logo.jpg'); ?>" class="small-avatar">
                        <div>
                            <strong><?php echo htmlspecialchars($currentUser['username']); ?></strong><br>
                            <small><?php echo htmlspecialchars($currentUser['email']); ?></small>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="dropdown-actions">
                    <a href="profile.php">Your profile</a>
                    <a href="upload.php">Create</a>
                    <a href="pinboard.php">Pinboard</a>
                </div>
                <div class="accounts-list">
                    <div class="accounts-title">All accounts</div>
                    <?php foreach ($accounts as $acc): ?>
                        <a class="account-item" href="switch_account.php?id=<?php echo (int)$acc['id']; ?>">
                            <img src="<?php echo htmlspecialchars($acc['profile_pic'] ?: 'image/logo.jpg'); ?>" class="avatar-xs">
                            <?php echo htmlspecialchars($acc['username']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <div class="dropdown-footer">
                    <a href="convert_business.php">Convert to business</a>
                    <a href="logout.php">Log out</a>
                </div>
            </div>
        </div>
    </header>

    <script>
// === Account dropdown toggle ===
document.addEventListener("DOMContentLoaded", function () {
  const profileBtn = document.querySelector(".profile-btn");
  const accountMenu = document.querySelector(".account-menu");

  if (profileBtn && accountMenu) {
    profileBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      accountMenu.classList.toggle("open");
    });

    document.addEventListener("click", () => {
      accountMenu.classList.remove("open");
    });
  }
});
</script>
</body>