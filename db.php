<?php
$host = "localhost";
$user = "yunus";
$pass = "emre";
$dbname = "yep_demo";

// MySQL bağlantısı
$conn = new mysqli($host, $user, $pass, $dbname);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Bağlantı başarısız: " . $conn->connect_error);
}

// UTF-8 karakter kodlaması ayarları
$conn->set_charset("utf8mb4");
$conn->query("SET NAMES utf8mb4");
$conn->query("SET CHARACTER SET utf8mb4");
$conn->query("SET collation_connection = 'utf8mb4_unicode_ci'");
?>
