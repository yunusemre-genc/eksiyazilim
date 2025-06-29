/**
 * ========================================
 * EKŞİ YAZILIM - ANA JAVASCRIPT DOSYASI
 * Tüm JavaScript fonksiyonları bu dosyada birleştirildi
 * ========================================
 */

console.log("Ekşi Yazılım'a hoş geldiniz!");

/**
 * ========================================
 * FORM VALİDASYONU
 * ========================================
 */

function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = 'red';
            isValid = false;
        } else {
            input.style.borderColor = '';
        }
    });

    return isValid;
}

/**
 * ========================================
 * POST İŞLEMLERİ
 * ========================================
 */

// Post silme onayı
function confirmDelete(postId) {
    if (confirm('Bu gönderiyi silmek istediğinizden emin misiniz?')) {
        window.location.href = `post_delete.php?id=${postId}`;
    }
}

// Post kopyalama onayı
function confirmCopy(postId) {
    if (confirm('Bu gönderiyi kopyalamak istediğinizden emin misiniz?')) {
        window.location.href = `post_copy.php?id=${postId}`;
    }
}

/**
 * ========================================
 * NAVBAR İŞLEMLERİ
 * ========================================
 */

function setActiveNavLink() {
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('nav a');

    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage ||
            (currentPage === '' && href === 'index.php') ||
            (currentPage === 'index.html' && href === 'index.php')) {
            link.style.fontWeight = 'bold';
            link.style.color = '#00c6ff';
        }
    });
}

/**
 * ========================================
 * INDEX SAYFASI İŞLEMLERİ
 * ========================================
 */

function initializeIndexPage() {
    // URL'den username'i al
    const params = new URLSearchParams(window.location.search);
    const username = params.get("username");

    // Kullanıcı adını göster
    const usernameElement = document.getElementById("kullaniciAdi");
    if (usernameElement && username) {
        usernameElement.innerText = username;
    }

    // Hata mesajı varsa göster
    const error = params.get("error");
    if (error === "user") {
        showAlert("❌ Kullanıcı bulunamadı!", "error");
    } else if (error === "password") {
        showAlert("❌ Şifre yanlış!", "error");
    }

    // Linkleri kullanıcı adıyla yönlendir
    updateLinksWithUsername(username);
}

function updateLinksWithUsername(username) {
    if (username) {
        const btnYeni = document.getElementById("btnYeni");
        const btnListe = document.getElementById("btnListe");
        const btnCikis = document.getElementById("btnCikis");

        if (btnYeni) btnYeni.href = `post_create.php?username=${encodeURIComponent(username)}`;
        if (btnListe) btnListe.href = `post_list.php?username=${encodeURIComponent(username)}`;
        if (btnCikis) btnCikis.href = `login.html`;
    }
}

/**
 * ========================================
 * REGISTER SAYFASI İŞLEMLERİ
 * ========================================
 */

function initializeRegisterPage() {
    // URL'den hata mesajlarını kontrol et
    const params = new URLSearchParams(window.location.search);
    const error = params.get('error');
    const success = params.get('success');

    const errorDiv = document.getElementById('error-message');
    const successDiv = document.getElementById('success-message');

    if (error && errorDiv) {
        let errorText = '';
        switch (error) {
            case 'empty_fields':
                errorText = 'Lütfen tüm alanları doldurun.';
                break;
            case 'invalid_email':
                errorText = 'Geçerli bir email adresi girin.';
                break;
            case 'duplicate':
                errorText = 'Bu kullanıcı adı veya email zaten kullanılıyor.';
                break;
            case 'registration_failed':
                errorText = 'Kayıt sırasında bir hata oluştu. Lütfen tekrar deneyin.';
                break;
            default:
                errorText = 'Bir hata oluştu.';
        }
        errorDiv.textContent = errorText;
        errorDiv.style.display = 'block';
    }

    if (success === 'registered' && successDiv) {
        successDiv.textContent = 'Kayıt başarılı! Giriş yapabilirsiniz.';
        successDiv.style.display = 'block';
    }
}

/**
 * ========================================
 * UTILITY FONKSİYONLAR
 * ========================================
 */

// Alert gösterme fonksiyonu
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;

    // Alert tipine göre renk
    switch (type) {
        case 'error':
            alertDiv.style.backgroundColor = '#e74c3c';
            break;
        case 'success':
            alertDiv.style.backgroundColor = '#2ecc71';
            break;
        case 'warning':
            alertDiv.style.backgroundColor = '#f39c12';
            break;
        default:
            alertDiv.style.backgroundColor = '#3498db';
    }

    document.body.appendChild(alertDiv);

    // 3 saniye sonra kaldır
    setTimeout(() => {
        alertDiv.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 300);
    }, 3000);
}

// Sayfa yüklendiğinde çalışacak ana fonksiyon
function initializeApp() {
    // Navbar aktif link işaretleme
    setActiveNavLink();

    // Form submit olaylarını dinle
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form.id)) {
                e.preventDefault();
                showAlert('Lütfen tüm gerekli alanları doldurun!', 'error');
            }
        });
    });

    // Sayfa tipine göre özel işlemler
    const currentPage = window.location.pathname.split('/').pop();

    if (currentPage === 'index.html' || currentPage === 'index.php' || currentPage === '') {
        initializeIndexPage();
    } else if (currentPage === 'register.html') {
        initializeRegisterPage();
    }

    // Smooth scroll için
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * ========================================
 * CSS ANİMASYONLARI
 * ========================================
 */

// CSS animasyonları için style ekle
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .post {
        transition: transform 0.2s ease;
    }

    .post:hover {
        transform: translateY(-2px);
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }
`;
document.head.appendChild(style);

/**
 * ========================================
 * SAYFA YÜKLENDİĞİNDE ÇALIŞTIR
 * ========================================
 */

// DOM yüklendiğinde uygulamayı başlat
document.addEventListener('DOMContentLoaded', initializeApp);

// Sayfa tamamen yüklendiğinde ek işlemler
window.addEventListener('load', function() {
    console.log('Sayfa tamamen yüklendi');

    // Performans optimizasyonu için lazy loading
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
});