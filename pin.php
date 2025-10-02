<?php
date_default_timezone_set('UTC');
include 'config.php';
session_start();

$pin_id = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'] ?? null;

if (!$pin_id) die("Pin not found.");

// --- Like / Save Actions ---
if ($user_id && isset($_GET['action'])) {
    $action = $_GET['action'];
    if (in_array($action, ['like', 'unlike', 'save', 'unsave'])) {
        $table = ($action === 'like' || $action === 'unlike') ? 'likes' : 'saves';
        if ($action === 'like' || $action === 'save') {
            $stmt = $conn->prepare("INSERT IGNORE INTO $table (user_id, pin_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $pin_id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("DELETE FROM $table WHERE user_id=? AND pin_id=?");
            $stmt->bind_param("ii", $user_id, $pin_id);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: pin.php?id=$pin_id");
        exit();
    }
}

// --- Comment Posting ---
if ($user_id && $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $stmt = $conn->prepare("INSERT INTO comments (user_id, pin_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $pin_id, $comment);
    $stmt->execute();
    $stmt->close();
    header("Location: pin.php?id=$pin_id");
    exit();
}

// --- Fetch Pin Data ---
$stmt = $conn->prepare("SELECT p.*, u.username, u.profile_pic FROM pins p JOIN users u ON p.user_id=u.id WHERE p.id=?");
$stmt->bind_param("i", $pin_id);
$stmt->execute();
$pin = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$pin) die("Pin not found.");

// --- Check Like/Save Status ---
$is_liked = $is_saved = false;
if ($user_id) {
    $stmt = $conn->prepare("SELECT 1 FROM likes WHERE user_id=? AND pin_id=?");
    $stmt->bind_param("ii", $user_id, $pin_id);
    $stmt->execute();
    $stmt->store_result();
    $is_liked = $stmt->num_rows > 0;
    $stmt->close();

    $stmt = $conn->prepare("SELECT 1 FROM saves WHERE user_id=? AND pin_id=?");
    $stmt->bind_param("ii", $user_id, $pin_id);
    $stmt->execute();
    $stmt->store_result();
    $is_saved = $stmt->num_rows > 0;
    $stmt->close();
}

// --- Counts ---
$total_likes = $conn->query("SELECT COUNT(*) FROM likes WHERE pin_id=$pin_id")->fetch_row()[0];
$total_comments = $conn->query("SELECT COUNT(*) FROM comments WHERE pin_id=$pin_id")->fetch_row()[0];

// --- Fetch Comments ---
$comments = [];
$stmt = $conn->prepare("SELECT c.*, u.username, u.profile_pic FROM comments c JOIN users u ON c.user_id=u.id WHERE c.pin_id=? ORDER BY c.created_at DESC");
$stmt->bind_param("i", $pin_id);
$stmt->execute();
$comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// --- More Like This ---
$relevant_pins = [];
$stmt = $conn->prepare("SELECT id, title, image_url FROM pins WHERE category=? AND id!=? ORDER BY RAND() LIMIT 8");
$stmt->bind_param("si", $pin['category'], $pin_id);
$stmt->execute();
$relevant_pins = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$current_user_pic = $_SESSION['profile_pic'] ?? 'assets/images/default-profile.png';
$pin_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

function time_elapsed_string($datetime) {
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $ago = new DateTime($datetime, new DateTimeZone('UTC'));
    $diff = $now->diff($ago);
    $diff->w = floor($diff->d / 7); $diff->d -= $diff->w * 7;
    $string = ['y'=>'year','m'=>'month','w'=>'week','d'=>'day','h'=>'hour','i'=>'minute','s'=>'second'];
    foreach ($string as $k => &$v) if ($diff->$k) $v=$diff->$k.' '.$v.($diff->$k>1?'s':''); else unset($string[$k]);
    return $string ? reset($string).' ago' : 'just now';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($pin['title']); ?></title>
<link rel="stylesheet" href="home.css">
<link rel="stylesheet" href="pin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include 'header2.php'; ?>

<main>
<div class="pin-wrapper">
<div class="pin-box">
<div class="pin-image-section">
    <img src="<?php echo htmlspecialchars($pin['image_url']); ?>" class="pin-image">
</div>
<div class="pin-details-section">

    <div class="pin-header-actions">
        <div class="pin-actions-left">
            <a href="<?php echo htmlspecialchars($pin['image_url']); ?>" download class="action-btn"><i class="fa-solid fa-download"></i></a>
            <button class="action-btn" onclick="navigator.clipboard.writeText('<?php echo $pin_url; ?>'); alert('Link copied');"><i class="fa-solid fa-share-nodes"></i></button>
        </div>
        <div class="pin-actions-right">
            <span class="like-count-display"><?php echo $total_likes; ?> Likes</span>
            <a href="pin.php?id=<?php echo $pin_id; ?>&action=<?php echo $is_liked?'unlike':'like'; ?>" class="like-btn">
                <i class="fa-heart <?php echo $is_liked?'fa-solid':'fa-regular'; ?>"></i>
            </a>
            <a href="pin.php?id=<?php echo $pin_id; ?>&action=<?php echo $is_saved?'unsave':'save'; ?>" class="save-btn <?php echo $is_saved?'saved':''; ?>">
                <?php echo $is_saved?'Saved':'Save'; ?>
            </a>
        </div>
    </div>

    <h1 class="pin-title"><?php echo htmlspecialchars($pin['title']); ?></h1>
    <p class="pin-description"><?php echo htmlspecialchars($pin['description']); ?></p>

    <div class="user-profile-info">
        <img src="<?php echo htmlspecialchars($pin['profile_pic']); ?>" class="profile-pic">
        <a href="profile.php?id=<?php echo $pin['user_id']; ?>"><?php echo htmlspecialchars($pin['username']); ?></a>
    </div>

    <!-- Comments Preview (Only 1 visible) -->
<div class="comments-section-launcher">
    <h3>Comments (<?php echo $total_comments; ?>)</h3>

    <?php if (!empty($comments)): ?>
        <div class="comment-preview">
            <img src="<?php echo htmlspecialchars($comments[0]['profile_pic']); ?>" class="comment-profile-pic">
            <div>
                <strong><?php echo htmlspecialchars($comments[0]['username']); ?></strong>
                <p><?php echo htmlspecialchars($comments[0]['comment']); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if(count($comments) > 1): ?>
        <button id="open-comments-slider" class="action-btn"><i class="fa-solid fa-chevron-up"></i></button>
    <?php endif; ?>
</div>

<!-- Comments Slider Overlay -->
<div id="comments-slider" class="comments-slider-overlay">
    <div class="comments-slider-content">
        <div class="comments-slider-header">
            <h3>All Comments</h3>
            <button id="close-comments-slider" class="action-btn"><i class="fa-solid fa-times"></i></button>
        </div>
        <div class="comments-list-scrollable">
            <?php foreach ($comments as $c): ?>
                <div class="comment">
                    <img src="<?php echo htmlspecialchars($c['profile_pic']); ?>" class="comment-profile-pic">
                    <div>
                        <strong><?php echo htmlspecialchars($c['username']); ?></strong>
                        <p><?php echo htmlspecialchars($c['comment']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
// === Comments slider open/close ===
document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.getElementById("open-comments-slider");
    const closeBtn = document.getElementById("close-comments-slider");
    const slider = document.getElementById("comments-slider");

    if(openBtn && slider) {
        openBtn.onclick = () => slider.classList.add("open");
    }
    if(closeBtn && slider) {
        closeBtn.onclick = () => slider.classList.remove("open");
    }
    if(slider) {
        slider.addEventListener("click", (e)=>{
            if(e.target === slider) slider.classList.remove("open");
        });
    }
});
</script>

</body>
</html>
