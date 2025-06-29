<?php
session_start();
$username = $_SESSION['username'] ?? null;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ekşi Yazılım - Ana Sayfa</title>
  <link rel="stylesheet" href="css/main.css" />
</head>
<body>
  <header>
    <h1><img src="image/logo.png">EKŞİ YAZILIM</h1>
    <nav>
      <?php if ($username): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="post_create.php">Yeni Gönderi</a>
        <a href="post_list.php">Gönderiler</a>
        <a href="logout.php">Çıkış Yap</a>
        <span>👋 Hoş geldin, <strong><?= htmlspecialchars($username) ?></strong></span>
      <?php else: ?>
        <a href="login.php">Giriş Yap</a>
        <a href="register.php">Kayıt Ol</a>
        <a href="post_list.php">Gönderileri Gör</a>
      <?php endif; ?>
    </nav>
  </header>

  <main>
    <h2>HOŞ GELDİN <span class="username"><?= $username ? htmlspecialchars($username) : 'ZİYARETÇİ' ?></span> 👋👨‍💻</h2>
    <h2>
      <p>Ekşi Yazılım'a kod paylaş, sorular sor, tartışmalara katıl!</p>
    </h2>
    <?php if (!$username): ?>
      <p><a href="login.php" class="btn">Giriş Yap</a> veya <a href="register.php" class="btn">Kayıt Ol</a></p>
    <?php endif; ?>
  </main>

  <footer>
    <p>© 2025 Ekşi Yazılım</p>
  </footer>

  <script src="script/app.js"></script>
</body>
</html>
