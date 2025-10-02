<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: pinbored.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $business_url = trim($_POST['business_url'] ?? '');

    if ($title === "" || $category === "") {
        $err = "Please fill all required fields.";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $err = "Please choose an image.";
    } else {
        $f = $_FILES['image'];
        $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
        if (!in_array($f['type'], $allowed)) {
            $err = "Only JPG/PNG/GIF/WebP allowed.";
        } else {
            $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
            $newName = 'uploads/' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;

            if (!is_dir('uploads')) mkdir('uploads', 0755);

            if (move_uploaded_file($f['tmp_name'], $newName)) {
                $stmt = $conn->prepare("INSERT INTO pins (user_id, title, description, image_url, category, business_url) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssss", $user_id, $title, $description, $newName, $category, $business_url);

                if ($stmt->execute()) {
                    header("Location: home.php");
                    exit;
                } else {
                    $err = "Database error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $err = "Upload failed.";
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Upload Pin</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f8f8;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin:0;
    }
    .upload-box {
      width: 400px;
      padding: 20px;
      border-radius: 16px;
      background: #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    h2 {
      margin-bottom: 20px;
      color: #333;
      text-align:center;
    }
    label { display: block; margin: 10px 0 5px; font-weight: bold; }
    input, select, textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    button {
      width: 100%;
      padding: 12px;
      background: #e60023;
      color: #fff;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      font-weight: bold;
    }
    button:hover {
      background: #ad081b;
    }
    .error {
      background: #ffe0e0;
      color: darkred;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="upload-box">
    <h2>Upload a Pin</h2>
    <?php if (!empty($err)): ?>
      <div class="error"><?php echo htmlspecialchars($err); ?></div>
    <?php endif; ?>

    <form action="upload.php" method="post" enctype="multipart/form-data">
      <label>Title *</label>
      <input type="text" name="title" required>

      <label>Description</label>
      <textarea name="description"></textarea>

      <label>Category *</label>
      <select name="category" required>
        <option value="">-- Select Category --</option>
        <option value="Fashion">Fashion</option>
        <option value="Food">Food</option>
        <option value="Travel">Travel</option>
        <option value="Business">Business</option>
        <option value="Education">Education</option>
        <option value="Technology">Technology</option>
        <option value="DIY">DIY</option>
        <option value="Quotes">Quotes</option>
      </select>

      <label>Upload Image *</label>
      <input type="file" name="image" accept="image/*" required>

      <label>Business Website (optional)</label>
      <input type="url" name="business_url" placeholder="https://example.com">

      <button type="submit">Upload Pin</button>
    </form>
  </div>
</body>
</html>
