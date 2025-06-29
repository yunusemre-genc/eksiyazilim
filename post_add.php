<?php
session_start();

// Giriş yapmamış kullanıcıları login sayfasına yönlendir
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once "session_check.php";
require_login();

// Veritabanı bağlantısı
include "db.php";
include "config.php";

// Form gönderildiyse işlem yap
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $user = get_logged_in_user();
    $user_id = $user['user_id'];

    if (!$user_id) {
        $error = "Kullanıcı bilgisi bulunamadı.";
    } else {
        // Post ekleme işlemi
        if ($USE_ADVANCED_DB_FEATURES) {
            // Stored Procedure kullanarak gönderiyi ekle
            $stmt = $conn->prepare("CALL ekle_post(?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $title, $content);
        } else {
            // Normal SQL sorgusu kullanarak gönderiyi ekle
            $success = add_post($conn, $user_id, $title, $content);
        }

        if ($USE_ADVANCED_DB_FEATURES) {
            if ($stmt->execute()) {
                header("Location: post_list.php"); // Başarılıysa gönderi listesine dön
                exit();
            } else {
                $error = "Gönderi eklenemedi: " . $stmt->error;
            }
        } else {
            if ($success) {
                header("Location: post_list.php"); // Başarılıysa gönderi listesine dön
                exit();
            } else {
                $error = "Gönderi eklenemedi!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Yeni Gönderi - Ekşi Yazılım</title>
  <link rel="stylesheet" href="css/main.css" />
</head>
<body>

<div class="form-container">
  <h2>Yeni Gönderi Ekle</h2>

  <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

  <form method="POST">
    <div class="form-group">
      <input type="text" name="title" placeholder="Gönderi Başlığı" required>
    </div>
    <div class="form-group">
      <textarea name="content" rows="6" placeholder="İçeriği buraya yaz..." required></textarea>
    </div>
    <button type="submit" class="btn btn-success">Gönderiyi Paylaş</button>
    <a href="post_list.php" class="btn btn-primary">← Gönderilere Dön</a>
  </form>
</div>

<script src="script/app.js"></script>
</body>
</html>
