<?php
session_start();

// Giriş yapmamış kullanıcıları login sayfasına yönlendir
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once "session_check.php";
require_login();

include "db.php";
include "config.php";

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $user = get_logged_in_user();
    $user_id = $user['user_id'];

    if (!empty($title) && !empty($content) && $user_id) {
        // Post ekleme işlemi
        if ($USE_ADVANCED_DB_FEATURES) {
            // Stored Procedure kullanımı
            $sql = "CALL ekle_post(?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $user_id, $title, $content);
        } else {
            // Normal SQL sorgusu kullanımı
            $success = add_post($conn, $user_id, $title, $content);
        }

        if ($USE_ADVANCED_DB_FEATURES) {
            if ($stmt->execute()) {
                header("Location: post_list.php");
                exit();
            } else {
                $error_message = "❌ Gönderi eklenemedi: " . $stmt->error;
            }
        } else {
            if ($success) {
                header("Location: post_list.php");
                exit();
            } else {
                $error_message = "❌ Gönderi eklenemedi!";
            }
        }
    } else {
        $error_message = "❗ Başlık ve içerik boş bırakılamaz!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Yeni Gönderi - Ekşi Yazılım</title>
  <link rel="stylesheet" href="css/main.css" />
</head>
<body>
  <header>
    <h1>Ekşi Yazılım</h1>
  </header>

  <h2>Yeni Gönderi Paylaş</h2>

  <?php if ($error_message): ?>
    <div class="form-container">
      <div class="error-message">
        <?= htmlspecialchars($error_message) ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="form-container">
    <form method="POST" action="post_create.php">
      <div class="form-group">
        <input type="text" name="title" placeholder="Konu Başlığı" required>
      </div>
      <div class="form-group">
        <textarea name="content" placeholder="Kodunuzu veya içeriğinizi buraya yazın..." rows="15" required></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Paylaş</button>
      <a href="post_list.php" class="btn btn-success">← Gönderilere Dön</a>
    </form>
  </div>

  <footer>
    © 2025 Ekşi Yazılım
  </footer>

  <script src="script/app.js"></script>
</body>
</html>
