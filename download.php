<?php
include 'config.php';
$pin_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($pin_id === 0) {
    die("Invalid pin ID.");
}
$stmt = $conn->prepare("SELECT image_url, title FROM pins WHERE id = ?");
$stmt->bind_param("i", $pin_id);
$stmt->execute();
$result = $stmt->get_result();
$pin = $result->fetch_assoc();
$stmt->close();
$conn->close();
if (!$pin) {
    die("Pin not found.");
}
$file_path = $pin['image_url'];
$file_name = basename($file_path);
if (file_exists($file_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit;
} else {
    die("Image not found.");
}
?>