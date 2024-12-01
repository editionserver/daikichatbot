<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daikin AI Asistanı</title>
    
    <!-- Material Kit CSS -->
    <link rel="stylesheet" href="assets/css/material-kit.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar bg-gradient-dark">
        <div class="sidebar-header">
            <button class="btn btn-icon btn-white w-100 mb-3 new-chat-btn">
                <i class="material-icons">add</i>
                Yeni Sohbet
            </button>
        </div>

        <div class="sidebar-chats">
            <!-- Dinamik olarak doldurulacak sohbet listesi -->
        </div>

        <div class="sidebar-footer">
            <?php if (!isLoggedIn()): ?>
                <button class="btn btn-outline-white w-100 mb-2 login-btn">
                    Giriş Yap
                </button>
            <?php else: ?>
                <button class="btn btn-outline-info w-100 mb-2 upgrade-btn">
                    Pro'ya Yükselt
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ana İçerik -->
    <div class="main-content">
        <!-- Üst Bar -->
        <nav class="navbar navbar-expand-lg bg-white py-3">
            <div class="container-fluid">
                <span class="navbar-brand">Daikin AI Asistanı</span>
                
                <?php if (isLoggedIn()): ?>
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" type="button" id="profileMenu" data-bs-toggle="dropdown">
                        <div class="avatar avatar-sm rounded-circle bg-gradient-primary">
                            <?php echo getUserInitials(); ?>
                        </div>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/profile">Profil</a></li>
                        <li><a class="dropdown-item" href="/settings">Ayarlar</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/logout">Çıkış Yap</a></li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </nav>

        <!-- Chat Alanı -->
        <div class="chat-container">
            <div class="chat-messages" id="chatMessages">
                <!-- Hoşgeldiniz Mesajı -->
                <div class="message system-message">
                    <p>Daikin klima sistemleri hakkında her türlü sorunuzu yanıtlamaya hazırım!</p>
                    
                    <?php if (!isLoggedIn()): ?>
                    <div class="guest-warning">
                        <p>Ücretsiz deneme: 2 mesaj hakkınız bulunmaktadır.</p>
                        <a href="/register" class="btn btn-primary btn-sm">Hemen Üye Ol</a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Örnek Sorular -->
                <div class="sample-queries">
                    <h6>Örnek Sorular:</h6>
                    <div class="query-buttons">
                        <button class="btn btn-outline-primary btn-sm query-btn">
                            RXYQ14 modelinin elektrik özellikleri nelerdir?
                        </button>
                        <button class="btn btn-outline-primary btn-sm query-btn">
                            Klima bakımı ne sıklıkla yapılmalıdır?
                        </button>
                        <button class="btn btn-outline-primary btn-sm query-btn">
                            En verimli Daikin modeli hangisidir?
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mesaj Gönderme Alanı -->
            <div class="chat-input">
                <form id="messageForm" class="message-form">
                    <div class="input-group">
                        <textarea 
                            class="form-control"
                            rows="1"
                            placeholder="Mesajınızı yazın..."
                            id="messageInput"
                        ></textarea>
                        <button type="submit" class="btn btn-primary">
                            <i class="material-icons">send</i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/material-kit.min.js"></script>
    <script src="assets/js/chat.js"></script>
</body>
</html>