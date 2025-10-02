<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $birthday = trim($_POST['birthday']);

    if ($username && $email && $password && $birthday) {
        // password rule
        if (strlen($password) < 8) {
            $_SESSION['error'] = "Password must be at least 8 characters.";
            $_SESSION['old'] = $_POST; // keep entered data
            header("Location: signup.php");
            exit;
        }

        // birthday rule
        $birthDate = DateTime::createFromFormat('Y-m-d', $birthday);
        $today = new DateTime();
        $minDate = (clone $today)->modify('-13 years');

        if (
            !$birthDate ||
            $birthDate > $today ||
            $birthDate > $minDate ||
            (int)$birthDate->format('Y') < 1900
        ) {
            $_SESSION['error'] = "Invalid or underage birthday.";
            $_SESSION['old'] = $_POST; // keep username & email
            unset($_SESSION['old']['password']); // clear password
            unset($_SESSION['old']['birthday']); // clear birthday
            header("Location: signup.php");
            exit;
        }

        // check if user exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['error'] = "User already exists with this email.";
            $_SESSION['old'] = $_POST;
            unset($_SESSION['old']['password']); // clear password
            $stmt->close();
            header("Location: signup.php");
            exit;
        }
        $stmt->close();

        // insert new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, birthday) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashedPassword, $birthday);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Account created successfully. Please login.";
            header("Location: pinboard.php");
            exit;
        } else {
            $_SESSION['error'] = "Error: " . $stmt->error;
            header("Location: signup.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "All fields are required.";
        $_SESSION['old'] = $_POST;
        unset($_SESSION['old']['password']); // clear password
        header("Location: signup.php");
        exit;
    }
}
?>
