<?php
session_start();

// Giriş yapmamış kullanıcıları login sayfasına yönlendir
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include "db.php";
include "config.php";

$post_id = $_GET['id'] ?? null;
if (!$post_id || !is_numeric($post_id)) {
    header("Location: post_list.php?error=invalid_id");
    exit();
}

// Post silme işlemi
if ($USE_ADVANCED_DB_FEATURES) {
    // Trigger otomatik çalışacak
    $sql = "DELETE FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
} else {
    // Manuel log tutma ile silme
    $success = delete_post_with_log($conn, $post_id);
}

if ($USE_ADVANCED_DB_FEATURES) {
    if ($stmt->execute()) {
        header("Location: post_list.php?success=deleted");
        exit();
    } else {
        header("Location: post_list.php?error=delete_failed");
        exit();
    }
} else {
    if ($success) {
        header("Location: post_list.php?success=deleted");
        exit();
    } else {
        header("Location: post_list.php?error=delete_failed");
        exit();
    }
}
?>
