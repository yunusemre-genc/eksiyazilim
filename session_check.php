<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
function require_login() {
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
}

// Kullanıcı giriş yapmışsa dashboard'a yönlendir
function require_logout() {
    if (isset($_SESSION['username'])) {
        header("Location: dashboard.php");
        exit();
    }
}

// Kullanıcı bilgilerini al
function get_logged_in_user() {
    if (isset($_SESSION['username'])) {
        return [
            'username' => $_SESSION['username'],
            'user_id' => $_SESSION['user_id'] ?? null
        ];
    }
    return null;
}
?>