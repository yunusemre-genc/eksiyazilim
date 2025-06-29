<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Basit validasyon
    if (empty($username) || empty($email) || empty($password)) {
        header("Location: register.php?error=empty_fields");
        exit();
    }

    // Email formatı kontrolü
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.php?error=invalid_email");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepared statement kullanarak SQL injection'ı önle
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        header("Location: login.php?success=registered");
        exit();
    } else {
        // Kullanıcı adı veya email zaten varsa
        if ($conn->errno == 1062) {
            header("Location: register.php?error=duplicate");
        } else {
            header("Location: register.php?error=registration_failed");
        }
        exit();
    }
}
?>