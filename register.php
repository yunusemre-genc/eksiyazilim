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
        case 'empty_fields':
            $error_message = 'Lütfen tüm alanları doldurun.';
            break;
        case 'invalid_email':
            $error_message = 'Geçerli bir email adresi girin.';
            break;
        case 'duplicate':
            $error_message = 'Bu kullanıcı adı veya email zaten kullanılıyor.';
            break;
        case 'registration_failed':
            $error_message = 'Kayıt sırasında bir hata oluştu. Lütfen tekrar deneyin.';
            break;
        default:
            $error_message = 'Bir hata oluştu.';
    }
}

if (isset($_GET['success']) && $_GET['success'] === 'registered') {
    $success_message = 'Kayıt başarılı! Giriş yapabilirsiniz.';
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kayıt Ol - Ekşi Yazılım</title>
  <link rel="stylesheet" href="css/main.css">
</head>
<body class="login-container">
  <div class="login-form">
    <h1>Kayıt Ol</h1>

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

    <form action="register_process.php" method="POST">
      <input type="text" name="username" placeholder="Kullanıcı Adı" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Şifre" required>
      <button type="submit">Kayıt Ol</button>
    </form>
    <a href="login.php">Zaten hesabın var mı? Giriş Yap</a>
    <a href="index.php">← Ana Sayfaya Dön</a>
  </div>

  <script src="script/app.js"></script>
</body>
</html>