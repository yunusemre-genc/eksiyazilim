<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Veritabanı bağlantısı
include "db.php";
include "config.php";

// Function kullanarak istatistikleri al
if ($USE_ADVANCED_DB_FEATURES) {
    $total_posts = $conn->query("SELECT toplam_post_sayisi() as total")->fetch_assoc()['total'];
} else {
    $total_posts = get_total_posts($conn);
}

// Kullanıcı bilgilerini al
$username = $_SESSION['username'];
$user_sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Ekşi Yazılım</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <header>
        <h1>Ekşi Yazılım - Dashboard</h1>
        <nav>
            <a href="dashboard.php">Anasayfa</a>
            <a href="post_create.php">Gönderi Oluştur</a>
            <a href="post_list.php">Gönderileri Gör</a>
            <a href="logout.php">Çıkış Yap</a>
        </nav>
    </header>

    <main>
        <div class="container">
            <h2>Hoş geldin, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h2>
            <p>Artık gönderi paylaşabilir, kod tartışmalarına katılabilirsin!</p>

            <div class="stats-container">
                <h3>📊 Platform İstatistikleri</h3>
                <p><strong>Toplam Gönderi Sayısı:</strong> <?= $total_posts ?></p>

                <?php if ($user): ?>
                <p><strong>Senin Gönderi Sayın:</strong> <?= $user['posts'] ?? 0 ?></p>
                <p><strong>Senin Yorum Sayın:</strong> <?= $user['comments'] ?? 0 ?></p>
                <p><strong>Senin Beğeni Sayın:</strong> <?= $user['likes'] ?? 0 ?></p>
                <?php endif; ?>
            </div>

            <div class="actions">
                <a href="post_create.php" class="btn btn-primary">➕ Yeni Gönderi Oluştur</a>
                <a href="post_list.php" class="btn btn-success">📚 Tüm Gönderileri Gör</a>
                <p style="margin-top: 20px; font-size: 14px; color: #666;">
                    💡 <strong>Yeni Özellik:</strong> Artık mevcut gönderileri kopyalayarak yeni gönderiler oluşturabilirsin!
                </p>
            </div>
        </div>
    </main>

    <footer>
        © 2025 Ekşi Yazılım
    </footer>

    <script src="script/app.js"></script>
</body>
</html>
