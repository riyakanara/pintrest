<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Pin | Pinterest Clone</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f8f8;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }
    .upload-container {
      background: #fff;
      border-radius: 16px;
      padding: 30px;
      width: 420px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }
    .upload-container h2 {
      text-align: center;
      color: #111;
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin: 12px 0 6px;
      font-weight: bold;
      color: #333;
    }
    input, select, textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      margin-bottom: 15px;
    }
    input[type="file"] {
      padding: 6px;
    }
    button {
      width: 100%;
      background: #e60023;
      color: #fff;
      border: none;
      padding: 14px;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.2s ease;
    }
    button:hover {
      background: #ad081b;
    }
  </style>
</head>
<body>
  <div class="upload-container">
    <h2>Upload a Pin</h2>
    <form action="upload.php" method="post" enctype="multipart/form-data">
      <label for="title">Title *</label>
      <input type="text" name="title" id="title" required>

      <label for="description">Description</label>
      <textarea name="description" id="description" rows="3" placeholder="Write something about your pin..."></textarea>

      <label for="category">Category *</label>
      <select name="category" id="category" required>
        <option value="">-- Select Category --</option>
        <option value="Fashion">Fashion</option>
        <option value="Food">Food</option>
        <option value="Travel">Travel</option>
        <option value="Business">Business</option>
        <option value="Education">Education</option>
        <option value="Technology">Technology</option>
        <option value="DIY">DIY</option>
        <option value="Quotes">Quotes</option>
        <option value="Art">Art</option>
        <option value="HomeDecor">Home Decor</option>
      </select>

      <label for="image">Upload Image *</label>
      <input type="file" name="image" id="image" accept="image/*" required>

      <label for="business_url">Business Website (optional)</label>
      <input type="url" name="business_url" id="business_url" placeholder="https://example.com">

      <button type="submit">Upload Pin</button>
    </form>
  </div>
</body>
</html>
