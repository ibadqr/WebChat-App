<?php
session_start();
include 'db.php'; // Pastikan ini ada

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Mendapatkan data pengguna
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Mendapatkan daftar pengguna untuk chat
$users_query = "SELECT * FROM users WHERE id != '$user_id'";
$users_result = mysqli_query($conn, $users_query);

// Memeriksa kesalahan pada query
if (!$users_result) {
    die("Query gagal: " . mysqli_error($conn));
}

// Mengambil semua pengguna dalam array
$users = mysqli_fetch_all($users_result, MYSQLI_ASSOC);

// Mendapatkan ID penerima dari query string
$receiver_id = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : null;

// Mendapatkan pesan antara pengguna
$messages = [];
if ($receiver_id) {
    $messages_query = "SELECT * FROM messages WHERE (sender_id = '$user_id' AND receiver_id = '$receiver_id') OR (sender_id = '$receiver_id' AND receiver_id = '$user_id') ORDER BY timestamp";
    $messages_result = mysqli_query($conn, $messages_query);

    // Memeriksa kesalahan pada query
    if ($messages_result) {
        $messages = mysqli_fetch_all($messages_result, MYSQLI_ASSOC);
    } else {
        die("Query gagal: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebChat App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f2f6;
            margin-top: 45px;
            margin-bottom: 25%;
        }
        .navbar {
            background-color: #ce2bff;
            color: #fff;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030; /* Untuk memastikan navbar di atas */
        }
        .navbar .navbar-brand, .navbar .btn {
            color: #fff;
        }
        .navbar .btn:hover {
            color: #333;
        }
        #sidebar {
            height: 100vh;
            padding-top: 50px;
            overflow-y: auto;
            background-color: #fff;
            border-right: 1px solid #ddd;
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            transition: all 0.3s;
            z-index: 1000;
        }
        #sidebar.active {
            left: 0;
        }
        .chat-box {
            height: calc(100vh - 70px);
            overflow-y: auto;
            padding: 15px;
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
        }
        .chat-message {
            max-width: 75%;
            padding: 5px 10px;
            border-radius: 5px;
            margin-bottom: 5px;
            word-wrap: break-word;
        }
        .chat-message.sent {
            align-self: flex-end;
            border: 1px solid #ce2bff;
            color: #333;
        }
        .chat-message.received {
            align-self: flex-start;
            border: 1px solid #9b9b9b;
            color: #333;
        }
        .message-input {
            border-top: 1px solid #ddd;
            padding: 8px 15px 20px 15px;
            background-color: #fff;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
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
        p {
            margin-bottom: 0;
            font-size: 0.9rem;
        }
        h6 {
            color: #ce2bff;
        }
        .text-muted {
            font-size: 0.6rem;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div id="sidebar">
        <div class="p-3">
            <h6>Daftar Pengguna</h6>
            <ul class="list-group list-group-flush">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <a href="index.php?receiver_id=<?php echo $user['id']; ?>" class="list-group-item list-group-item-action <?php echo $receiver_id == $user['id'] ? 'active-user' : ''; ?>">
                            <?php echo htmlspecialchars($user['username']); ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item text-center">Tidak ada pengguna yang ditemukan.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <button class="btn btn-outline-light me-2" id="toggleSidebar">☰</button>
            <span class="navbar-brand mb-0 h1">WebChat App</span>
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </nav>

    <!-- Main Chat Area -->
    <div class="chat-box">
        <?php if ($receiver_id): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="chat-message <?php echo $msg['sender_id'] === $user_id ? 'sent' : 'received'; ?>">
                    <p><?php echo htmlspecialchars($msg['message']); ?></p>
                    <p class="text-muted"><?php echo date("H:i", strtotime($msg['timestamp'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center mt-5">
                <h6>Pilih pengguna untuk memulai obrolan!</h6>
            </div>
        <?php endif; ?>
    </div>

    <!-- Message Input -->
    <?php if ($receiver_id): ?>
        <form method="POST" action="send_message.php" class="message-input">
            <h6 style="text-align: center;">Obrolan: <?php echo htmlspecialchars($users[array_search($receiver_id, array_column($users, 'id'))]['username']); ?></h6>
            <input type="hidden" name="receiver_id" value="<?php echo $receiver_id; ?>">
            <div class="input-group">
                <input type="text" name="message" class="form-control" placeholder="Tulis pesan..." required>
                <button type="submit" class="btn btn-primary">Kirim</button>
            </div>
        </form>
    <?php endif; ?>

    <script>
        // Sidebar toggle functionality
        document.getElementById("toggleSidebar").addEventListener("click", function() {
            document.getElementById("sidebar").classList.toggle("active");
        });
    </script>
</body>
</html>