<?php
$host = "localhost";
$user = "root";       // your MySQL username
$pass = "";           // your MySQL password (set it if you have one)
$db   = "pinterest_clone"; // your database name

$conn = new mysqli($host, $user, $pass, $db,3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
