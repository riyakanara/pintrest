<?php
include 'config.php';
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}
$user_id = $_SESSION['user_id'];
$pin_id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;
if (!$pin_id || !in_array($action, ['like', 'unlike'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit();
}
try {
    if ($action === 'like') {
        $stmt = $conn->prepare("INSERT INTO likes (user_id, pin_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $pin_id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => 'Pin liked.']);
    } else if ($action === 'unlike') {
        $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND pin_id = ?");
        $stmt->bind_param("ii", $user_id, $pin_id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => 'Pin unliked.']);
    }
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
        echo json_encode(['status' => 'error', 'message' => 'You have already liked this pin.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
$conn->close();
?>