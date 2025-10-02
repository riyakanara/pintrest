<?php
include 'config.php';
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}
$user_id = $_SESSION['user_id'];
$pin_id = $_POST['pin_id'] ?? null;
$comment = $_POST['comment'] ?? null;
if (!$pin_id || empty($comment)) {
    echo json_encode(['status' => 'error', 'message' => 'Pin ID and comment are required.']);
    exit();
}
try {
    $stmt = $conn->prepare("INSERT INTO comments (user_id, pin_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $pin_id, $comment);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Comment posted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to post comment.']);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
$conn->close();
?>