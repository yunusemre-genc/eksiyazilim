<?php
session_start();

// Giriş yapmamış kullanıcıları login sayfasına yönlendir
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include "db.php";

// GET ile gelen ID
$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    die("Geçersiz gönderi ID'si.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Prepared statement kullanarak güvenli güncelleme
    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $post_id);

    if ($stmt->execute()) {
        header("Location: post_list.php");
        exit();
    } else {
        $error = "Güncelleme hatası: " . $stmt->error;
    }
}

// Gönderi bilgisi al (prepared statement ile)
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows !== 1) {
    die("Gönderi bulunamadı.");
}
$post = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Gönderi Düzenle</title>
  <link rel="stylesheet" href="css/main.css" />
</head>
<body>
  <div class="form-container">
    <h2>Gönderiyi Düzenle</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
      <div class="form-group">
        <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
      </div>
      <div class="form-group">
        <textarea name="content" rows="6" required><?= htmlspecialchars($post['content']) ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Güncelle</button>
      <a href="post_list.php" class="btn btn-success">← Geri Dön</a>
    </form>
  </div>

  <script src="script/app.js"></script>
</body>
</html>
