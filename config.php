<?php
// Veritabanı özelliklerini kontrol eden konfigürasyon dosyası
// Ücretsiz hosting sağlayıcıları için ayarlar

// Gelişmiş veritabanı özelliklerini kullan (true/false)
// Ücretsiz hosting için false yapın
$USE_ADVANCED_DB_FEATURES = true;

// Eğer gelişmiş özellikler kullanılamıyorsa, normal SQL sorguları kullanılacak
if (!$USE_ADVANCED_DB_FEATURES) {
    // Normal SQL sorguları için fonksiyonlar

    // Toplam post sayısını al
    function get_total_posts($conn) {
        $result = $conn->query("SELECT COUNT(*) as total FROM posts");
        return $result->fetch_assoc()['total'];
    }

    // İçerik uzunluğunu hesapla
    function get_content_length($conn, $post_id) {
        $stmt = $conn->prepare("SELECT CHAR_LENGTH(content) as length FROM posts WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['length'];
    }

    // Post ekle ve kullanıcı istatistiğini güncelle
    function add_post($conn, $user_id, $title, $content) {
        // Transaction başlat
        $conn->begin_transaction();

        try {
            // Post ekle
            $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $title, $content);
            $stmt->execute();

            // Kullanıcı istatistiğini güncelle
            $stmt = $conn->prepare("UPDATE users SET posts = posts + 1 WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            // Transaction'ı tamamla
            $conn->commit();
            return true;
        } catch (Exception $e) {
            // Hata durumunda rollback
            $conn->rollback();
            return false;
        }
    }

    // Post detaylarını al (view yerine)
    function get_post_details($conn) {
        return $conn->query("
            SELECT
                p.id,
                p.title,
                p.content,
                u.username,
                p.created_at
            FROM posts p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
        ");
    }

    // Post sil ve log tut
    function delete_post_with_log($conn, $post_id) {
        // Önce post bilgilerini al
        $stmt = $conn->prepare("SELECT title FROM posts WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $post = $result->fetch_assoc();

        if ($post) {
            // Transaction başlat
            $conn->begin_transaction();

            try {
                // Post'u sil
                $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
                $stmt->bind_param("i", $post_id);
                $stmt->execute();

                // Log kaydı ekle
                $message = "Gönderi silindi. ID: " . $post_id . " - Başlık: " . $post['title'];
                $stmt = $conn->prepare("INSERT INTO post_logs (message, deleted_at) VALUES (?, NOW())");
                $stmt->bind_param("s", $message);
                $stmt->execute();

                // Transaction'ı tamamla
                $conn->commit();
                return true;
            } catch (Exception $e) {
                // Hata durumunda rollback
                $conn->rollback();
                return false;
            }
        }

        return false;
    }
}
?>