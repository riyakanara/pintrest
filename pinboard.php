<?php
// pinboard.php (login)
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = $_POST['password']; 

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, username, password, profile_pic, interests FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // set session with consistent keys
                $_SESSION['user_id'] = (int)$row['id'];
                $_SESSION['email'] = $email;
                $_SESSION['username'] = $row['username'];
                $_SESSION['profile_pic'] = $row['profile_pic'];

                if (!empty($_POST['remember'])) {
                    setcookie("email", $email, time() + (86400 * 30), "/");
                }

                // if user has no interests saved -> go to interest selection
                $interests = trim($row['interests'] ?? '');
                if ($interests === '') {
                    header("Location: intrest.php");
                } else {
                    header("Location: home.php");
                }
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
        $stmt->close();
    } else {
        $error = "Please enter email and password.";
    }
}
?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pinterest Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php if (isset($_SESSION['success'])): ?>
  <div style="background:#d4edda; color:#155724; padding:10px; margin:10px auto; width:350px; border-radius:6px; text-align:center; font-weight:bold;">
    <?= $_SESSION['success']; ?>
  </div>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
  <div style="background:#f8d7da; color:#721c24; padding:10px; margin:10px auto; width:350px; border-radius:6px; text-align:center; font-weight:bold;">
    <?= $_SESSION['error']; ?>
  </div>
  <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- your existing login markup -->
<div class="container">
  <div class="left-section"></div>
  <div class="right-section">
    <div class="login-card">
      <h2>Welcome to Pinterest</h2>
      <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
      <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <a href="#" class="forgot">Forgot your password?</a>
        <label><input type="checkbox" name="remember"> Remember me</label>
        <button type="submit" class="login-btn">Log in</button>
        <p class="or">OR</p>
        <button type="button" class="facebook-btn">Continue with Facebook</button>
        <button type="button" class="google-btn">Continue with Google</button>
        <p class="terms">
          By continuing, you agree to Pinterest's
          <a href="#">Terms of Service</a> and 
          <a href="#">Privacy Policy</a>.
        </p>
        <p class="signup">Not on Pinterest yet? <a href="signup.php">Sign up</a></p>
        <p class="business">Are you a business? <a href="#">Get started here!</a></p>
      </form>
    </div>
  </div>
</div>
</body>
</html>
