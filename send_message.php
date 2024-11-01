<?php
session_start();
include 'db.php'; // Pastikan ini ada

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Mengambil data pengguna
$user_id = $_SESSION['user_id'];
$receiver_id = isset($_POST['receiver_id']) ? $_POST['receiver_id'] : null;
$message = isset($_POST['message']) ? $_POST['message'] : null;

// Memastikan semua data ada sebelum menyimpan
if ($receiver_id && $message) {
    // Menyimpan pesan ke database
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $user_id, $receiver_id, $message);
    
    if ($stmt->execute()) {
        header("Location: index.php?receiver_id=" . $receiver_id);
        exit();
    } else {
        die("Error: " . $stmt->error);
    }
} else {
    die("Missing receiver ID or message.");
}
?>