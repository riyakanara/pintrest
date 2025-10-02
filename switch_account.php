<?php
// switch_account.php
include 'db.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit;
}
$id = (int)$_GET['id'];
// very simple switch (for development/testing only)
$_SESSION['user_id'] = $id;
$redirect = $_SERVER['HTTP_REFERER'] ?? 'home.php';
header("Location: " . $redirect);
exit;
