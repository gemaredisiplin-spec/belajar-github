<?php
require_once 'config/database.php';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare("INSERT INTO guestbook_messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $message]);

    header("Location: guestbook.php?success=1");
    exit;
} else {
    header("Location: guestbook.php");
    exit;
}
