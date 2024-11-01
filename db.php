<?php
$host = 'localhost:3306'; // Ganti dengan host database Anda
$username = 'root'; // Ganti dengan username database Anda
$password = 'root'; // Ganti dengan password database Anda
$database = 'chat_app'; // Ganti dengan nama database Anda

// Membuat koneksi
$conn = mysqli_connect($host, $username, $password, $database);

// Memeriksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>