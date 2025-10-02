<?php
// intrest.php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: pinboard.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = $_POST['interests'] ?? [];
    // sanitize and normalize
    $allowed = ['Food','Art','Beauty','DIY & Crafts','Design','Thoughts','Quotes','Fashion','HomeDecor','Photography','Festivals','Culture','DIY','Food & Recipes'];
    $clean = [];
    foreach ($selected as $s) {
        $s = trim($s);
        if ($s === '') continue;
        if (in_array($s, $allowed)) $clean[] = $s;
        else $clean[] = htmlspecialchars($s); // allow additional if needed
    }
    $interests_csv = implode(',', array_unique($clean));
    $stmt = $conn->prepare("UPDATE users SET interests = ? WHERE id = ?");
    $stmt->bind_param("si", $interests_csv, $user_id);
    if ($stmt->execute()) {
        header("Location: home.php");
        exit();
    } else {
        $error = "Could not save interests: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Select Interests</title>
  <style>
    /* keep your existing styles */
    body { font-family: Arial, sans-serif; padding:40px; background:#fff;}
    h2 { text-align:center; margin-bottom:30px;}
    .categories { display:grid; grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); gap:20px; max-width:1000px; margin:0 auto;}
    .category { position:relative; border-radius:16px; overflow:hidden; cursor:pointer; }
    .category input { display:none; }
    .category img { width:100%; height:200px; object-fit:cover; display:block; transition:.3s;}
    .category span { position:absolute; bottom:10px; left:10px; font-size:1.2rem; font-weight:bold; color:#fff; text-shadow:0 2px 5px rgba(0,0,0,.6);}
    .category input:checked + img { filter:brightness(70%); transform:scale(1.05); }
    .submit-btn { display:block; margin:30px auto; padding:12px 30px; background:#e60023; color:#fff; border:none; border-radius:8px; cursor:pointer;}
  </style>
</head>
<body>
  <h2>Select the categories you’re interested in</h2>
  <?php if(isset($error)) echo "<p style='color:red;text-align:center;'>$error</p>"; ?>
  <form method="POST">
    <div class="categories">
      <?php
      // list of available options and images
      $options = [
        'Food'=>'image/2.jpg','Art'=>'image/4.jpg','Beauty'=>'image/23.jpg',
        'DIY & Crafts'=>'image/8.jpg','Design'=>'image/5.jpg','Thoughts'=>'image/6.jpg',
        'Quotes'=>'image/7.jpg','Fashion'=>'image/20.jpg','HomeDecor'=>'image/10.jpg',
        'Photography'=>'image/13.jpg','Festivals'=>'image/14.jpg','Culture'=>'image/11.jpg'
      ];
      foreach ($options as $label=>$img): ?>
        <label class="category">
          <input type="checkbox" name="interests[]" value="<?php echo htmlspecialchars($label); ?>">
          <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($label); ?>">
          <span><?php echo htmlspecialchars($label); ?></span>
        </label>
      <?php endforeach; ?>
    </div>
    <button class="submit-btn" type="submit">See Ideas for You</button>
  </form>
</body>
</html>
