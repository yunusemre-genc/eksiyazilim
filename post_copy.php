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

$post_id = $_GET['id'] ?? null;
$error_message = '';
$success_message = '';

if (!$post_id || !is_numeric($post_id)) {
    header("Location: post_list.php?error=invalid_id");
    exit();
}

// Orijinal gönderiyi al
$sql = "SELECT * FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows !== 1) {
    header("Location: post_list.php?error=post_not_found");
    exit();
}

$original_post = $result->fetch_assoc();

// Form gönderildiyse kopyalama işlemini yap
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $user = get_logged_in_user();
    $user_id = $user['user_id'];

    if (!empty($title) && !empty($content) && $user_id) {
        // Post ekleme işlemi
        if ($USE_ADVANCED_DB_FEATURES) {
            // Stored Procedure kullanarak kopya gönderiyi ekle
            $sql = "CALL ekle_post(?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $user_id, $title, $content);
        } else {
            // Normal SQL sorgusu kullanarak kopya gönderiyi ekle
            $success = add_post($conn, $user_id, $title, $content);
        }

        if ($USE_ADVANCED_DB_FEATURES) {
            if ($stmt->execute()) {
                header("Location: post_list.php?success=copied");
                exit();
            } else {
                $error_message = "❌ Gönderi kopyalanamadı: " . $stmt->error;
            }
        } else {
            if ($success) {
                header("Location: post_list.php?success=copied");
                exit();
            } else {
                $error_message = "❌ Gönderi kopyalanamadı!";
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
  <title>Gönderi Kopyala - Ekşi Yazılım</title>
  <link rel="stylesheet" href="css/main.css" />
</head>
<body>
  <header>
    <h1>Ekşi Yazılım</h1>
  </header>

  <h2>📋 Gönderi Kopyala</h2>

  <?php if ($error_message): ?>
    <div class="form-container">
      <div class="error-message">
        <?= htmlspecialchars($error_message) ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="form-container">
    <div class="original-post-info">
      <h4>📄 Orijinal Gönderi:</h4>
      <p><strong>Başlık:</strong> <?= htmlspecialchars($original_post['title']) ?></p>
      <p><strong>İçerik:</strong> <?= nl2br(htmlspecialchars(substr($original_post['content'], 0, 200))) ?>...</p>
      <small>🕒 <?= $original_post['created_at'] ?></small>
    </div>

    <form method="POST" action="post_copy.php?id=<?= $post_id ?>">
      <div class="form-group">
        <label for="title">Yeni Başlık:</label>
        <input type="text" name="title" id="title" placeholder="Kopya gönderi başlığı" value="Kopya: <?= htmlspecialchars($original_post['title']) ?>" required>
      </div>
      <div class="form-group">
        <label for="content">Yeni İçerik:</label>
        <textarea name="content" id="content" placeholder="Kopya gönderi içeriği" rows="15" required><?= htmlspecialchars($original_post['content']) ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary">📋 Gönderiyi Kopyala</button>
      <a href="post_list.php" class="btn btn-success">← Gönderilere Dön</a>
    </form>
  </div>

  <footer>
    © 2025 Ekşi Yazılım
  </footer>

  <script src="script/app.js"></script>
</body>
</html>