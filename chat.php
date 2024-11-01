<?php
session_start();
header('Content-Type: application/json');

$filename = 'messages.json';
$messages = json_decode(file_get_contents($filename), true) ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newMessage = [
        "user" => $_SESSION['username'],
        "message" => htmlspecialchars($_POST['message']),
        "timestamp" => date("Y-m-d H:i:s")
    ];
    $messages[] = $newMessage;
    file_put_contents($filename, json_encode($messages));
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode($messages);
}
?>