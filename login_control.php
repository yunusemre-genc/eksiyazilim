<?php
session_start(); // ← OLUŞTURULAN EN ÜST SATIRDA OLMALI

// Zaten giriş yapmışsa dashboard'a yönlendir
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        header("Location: login.php?error=empty");
        exit();
    }

    // Prepared statement kullan
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Şifre kontrolü (hash'lenmiş şifre için password_verify kullan)
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            // Giriş başarılı → kullanıcıyı SESSION'a ata
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['id'];

            // Dashboard'a yönlendir
            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: login.php?error=password");
            exit();
        }
    } else {
        header("Location: login.php?error=user");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    // GET isteği ile gelirse login sayfasına yönlendir
    header("Location: login.php");
    exit();
}
?>