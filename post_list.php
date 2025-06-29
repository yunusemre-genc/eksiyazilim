<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Veritabanı bağlantısı
include "db.php";
include "config.php";

// Function kullanımı - Toplam post sayısını al
if ($USE_ADVANCED_DB_FEATURES) {
    $total_posts = $conn->query("SELECT toplam_post_sayisi() as total")->fetch_assoc()['total'];
} else {
    $total_posts = get_total_posts($conn);
}

// View kullanımı - Post detaylarını al
if ($USE_ADVANCED_DB_FEATURES) {
    $result = $conn->query("
      SELECT * FROM view_post_details
      ORDER BY created_at DESC
    ");
} else {
    $result = get_post_details($conn);
}

if (!$result) {
    die("Sorgu hatası: " . $conn->error);
}

// Mesajları kontrol et
$message = '';
$message_type = '';
if (isset($_GET['success']) && $_GET['success'] === 'deleted') {
    $message = 'Gönderi başarıyla silindi!';
    $message_type = 'success';
} elseif (isset($_GET['success']) && $_GET['success'] === 'copied') {
    $message = 'Gönderi başarıyla kopyalandı!';
    $message_type = 'success';
} elseif (isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'invalid_id':
            $message = 'Geçersiz gönderi ID\'si.';
            break;
        case 'delete_failed':
            $message = 'Gönderi silinirken bir hata oluştu.';
            break;
        case 'post_not_found':
            $message = 'Gönderi bulunamadı.';
            break;
        default:
            $message = 'Bir hata oluştu.';
    }
    $message_type = 'error';
}

// Kullanıcı giriş durumunu kontrol et
$username = $_SESSION['username'] ?? null;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Gönderiler - Ekşi Yazılım</title>
  <link rel="stylesheet" href="css/main.css" />
</head>
<body>

<header>
  <h1>📚 Gönderiler - Ekşi Yazılım</h1>
  <nav>
    <?php if ($username): ?>
      <a href="dashboard.php">Dashboard</a>
      <a href="post_create.php">Yeni Gönderi</a>
      <a href="post_list.php">Gönderiler</a>
      <a href="logout.php">Çıkış Yap</a>
      <span>👋 Hoş geldin, <strong><?= htmlspecialchars($username) ?></strong></span>
    <?php else: ?>
      <a href="index.php">Ana Sayfa</a>
      <a href="login.php">Giriş Yap</a>
      <a href="register.php">Kayıt Ol</a>
      <a href="post_list.php">Gönderileri Gör</a>
    <?php endif; ?>
  </nav>
  <p>Toplam <?= $total_posts ?> gönderi bulundu</p>
</header>

<?php if ($message): ?>
  <div class="container">
    <div class="<?= $message_type ?>-message">
      <?= htmlspecialchars($message) ?>
    </div>
  </div>
<?php endif; ?>

<div class="container">
  <?php if ($username): ?>
    <div style="text-align: center; margin-bottom: 30px;">
      <a href="post_create.php" class="btn btn-primary" style="font-size: 18px; padding: 15px 30px;">➕ Yeni Gönderi Oluştur</a>
    </div>
  <?php endif; ?>

  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()) : ?>
      <div class="post">
        <strong>👤 <?= htmlspecialchars($row['username'] ?? 'Anonim') ?></strong>
        <h3><?= htmlspecialchars($row['title']) ?></h3>
        <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
        <small>🕒 <?= $row['created_at'] ?></small>

        <?php
        // Function kullanımı - İçerik uzunluğunu göster
        if ($USE_ADVANCED_DB_FEATURES) {
            $content_length = $conn->query("SELECT icerik_uzunlugu({$row['id']}) as length")->fetch_assoc()['length'];
        } else {
            $content_length = get_content_length($conn, $row['id']);
        }
        ?>
        <small>📏 İçerik uzunluğu: <?= $content_length ?> karakter</small>

        <?php if ($username): ?>
          <div class="actions">
            <a class="edit" href="post_edit.php?id=<?= $row['id'] ?>">✏️ Düzenle</a>
            <a class="copy" href="post_copy.php?id=<?= $row['id'] ?>" onclick="return confirmCopy(<?= $row['id'] ?>)">📋 Kopyala</a>
            <a class="delete" href="post_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Silmek istediğine emin misin?')">🗑️ Sil</a>
          </div>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="post">
      <h3>Henüz Gönderi Yok</h3>
      <p>İlk gönderiyi sen paylaş!</p>
      <?php if ($username): ?>
        <div class="actions">
          <a href="post_create.php" class="add-new">➕ İlk Gönderiyi Paylaş</a>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<footer>
  © 2025 Ekşi Yazılım
</footer>

<script src="script/app.js"></script>
</body>
</html>
