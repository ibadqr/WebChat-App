<?php
session_start();
include 'db.php'; // Pastikan file ini mengatur koneksi ke database

// Proses pendaftaran
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input
    if (!empty($username) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Cek apakah username sudah ada
            $query = "SELECT * FROM users WHERE username = '$username'";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) == 0) {
                // Insert pengguna baru ke database
                $insert_query = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
                if (mysqli_query($conn, $insert_query)) {
                    $_SESSION['user_id'] = mysqli_insert_id($conn); // Simpan ID pengguna
                    header("Location: index.php"); // Redirect ke halaman chat
                    exit();
                } else {
                    $error = "Pendaftaran gagal. Silakan coba lagi.";
                }
            } else {
                $error = "Username sudah terdaftar.";
            }
        } else {
            $error = "Password tidak cocok.";
        }
    } else {
        $error = "Semua field harus diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - WebChat App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #ce2bff; 
            border: 1px solid #ce2bff;
        }
        .btn-primary:hover {
            background-color: #fff;
            border: 1px solid #ce2bff;
            color: #ce2bff;
        }
        a, h3 {
            color: #ce2bff;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h3 class="text-center">Daftar WebChat App</h3></br>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
            <p class="mt-3 text-center">Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </form>
    </div>
</body>
</html>