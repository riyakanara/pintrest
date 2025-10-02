<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up - Pinterest Clone</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: url('image/bg.jpg') no-repeat center center/cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      backdrop-filter: blur(6px);
    }
    .signup-box {
      position: relative;
      background: #fff;
      padding: 40px 30px;
      border-radius: 15px;
      width: 350px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      text-align: center;
    }
    .signup-box h2 {
      margin-bottom: 20px;
      font-size: 22px;
      color: #333;
    }
    .signup-box input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }
    .signup-box button {
      width: 100%;
      padding: 12px;
      background: #e60023;
      border: none;
      border-radius: 8px;
      color: #fff;
      font-size: 15px;
      cursor: pointer;
      margin-top: 10px;
    }
    .signup-box button:hover {
      background: #c2001d;
    }
    .cancel-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 20px;
      color: #999;
      text-decoration: none;
    }
    .cancel-btn:hover {
      color: #000;
    }
    .message {
      font-size: 14px;
      margin-bottom: 10px;
    }
    .error {
      color: red;
    }
    .success {
      color: green;
    }
  </style>
</head>
<body>
  <div class="signup-box">
    <a href="pinboard.php" class="cancel-btn">✖</a>
    <h2>Welcome to Pinterest Clone</h2>

    <!-- Show error or success -->
    <?php if (isset($_SESSION['error'])): ?>
      <p class="message error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
      <p class="message success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <form action="signup_action.php" method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <label style="display:block; text-align:left; margin-top:10px;">Birthday</label>
      <input type="date" name="birthday" required>
      <button type="submit">Sign Up</button>
    </form>
    <p style="margin-top:15px; font-size:13px;">Already have an account? <a href="login.php">Log in</a></p>
  </div>
</body>
</html>
