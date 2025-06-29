<?php
session_start();

// Zaten giriş yapmışsa dashboard'a yönlendir
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

$error_message = '';
$success_message = '';

// URL'den hata mesajlarını kontrol et (GET parametresi ile gelen)
if (isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'password':
            $error_message = 'Hatalı şifre girdiniz.';
            break;
        case 'user':
            $error_message = 'Kullanıcı bulunamadı.';
            break;
        case 'empty':
            $error_message = 'Kullanıcı adı ve şifre gereklidir.';
            break;
        default:
            $error_message = 'Bir hata oluştu.';
    }
}

// URL'den başarı mesajlarını kontrol et
if (isset($_GET['success']) && $_GET['success'] === 'registered') {
    $success_message = 'Kayıt başarılı! Giriş yapabilirsiniz.';
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Giriş Yap - Ekşi Yazılım</title>
  <link rel="stylesheet" href="css/main.css" />
</head>
<body class="login-container">
  <div class="login-form">
    <h1>Giriş Yap</h1>

    <?php if ($error_message): ?>
      <div class="error-message">
        <?= htmlspecialchars($error_message) ?>
      </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
      <div class="success-message">
        <?= htmlspecialchars($success_message) ?>
      </div>
    <?php endif; ?>

    <form action="login_control.php" method="POST">
      <input type="text" name="username" placeholder="Kullanıcı Adı" required />
      <input type="password" name="password" placeholder="Şifre" required />
      <button type="submit">Giriş</button>
    </form>
    <a href="register.php">Hesabın yok mu? Kayıt Ol</a>
    <a href="index.php">← Ana Sayfaya Dön</a>
  </div>

  <script src="script/app.js"></script>
</body>
</html>
