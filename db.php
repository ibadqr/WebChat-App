<?php
$host = 'sql301.infinityfree.com:3306'; // Ganti dengan host database Anda
$username = 'if0_36788229'; // Ganti dengan username database Anda
$password = 'hUqx2fdCqsQ7Ck2'; // Ganti dengan password database Anda
$database = 'if0_36788229_chat_app'; // Ganti dengan nama database Anda

// Membuat koneksi
$conn = mysqli_connect($host, $username, $password, $database);

// Memeriksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
