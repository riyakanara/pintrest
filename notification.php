<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Notifications</title>
  <link rel="stylesheet" href="home.css">
  <style>
    .notif-container {
      margin-left: 90px;
      margin-top: 80px;
      padding: 20px;
    }
    .notif {
      background: #f9f9f9;
      border: 1px solid #ddd;
      padding: 10px;
      margin: 8px 0;
      border-radius: 8px;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <a href="home.php">🏠 Home</a>
    <a href="profile.php">👤 Profile</a>
    <a href="upload.php">📌 Upload</a>
    <a href="notifications.php">🔔 Notifications</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <div class="notif-container">
    <h2>Your Notifications</h2>
    <div class="notif">🔔 Someone liked your pin</div>
    <div class="notif">💬 New comment on your pin</div>
    <div class="notif">📌 Your pin was saved by a user</div>
  </div>
</body>
</html>
