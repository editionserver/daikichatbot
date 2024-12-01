document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const newChatBtn = document.querySelector('.new-chat-btn');
    let currentChatId = null;

    // Otomatik yükseklik ayarı
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });

    // Yeni sohbet başlatma
    newChatBtn.addEventListener('click', async () => {
        try {
            const response = await fetch('/api/chat/new', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            if (data.chat_id) {
                currentChatId = data.chat_id;
                chatMessages.innerHTML = ''; // Sohbet alanını temizle
                addSystemMessage('Yeni sohbet başlatıldı!');
                updateSidebarChats();
            }
        } catch (error) {
            console.error('Yeni sohbet oluşturulurken hata:', error);
            showError('Sohbet başlatılamadı. Lütfen tekrar deneyin.');
        }
    });

    // Mesaj gönderme
    messageForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        // Kullanıcı mesajını göster
        addUserMessage(message);
        messageInput.value = '';
        messageInput.style.height = 'auto';

        // Yükleniyor animasyonu
        const loadingMessage = addLoadingMessage();

        try {
            const response = await fetch('/api/chat/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    chat_id: currentChatId,
                    message: message
                })
            });

            const data = await response.json();
            
            // Yükleniyor mesajını kaldır
            loadingMessage.remove();

            if (data.error) {
                showError(data.error);
                return;
            }

            // Bot yanıtını göster
            addBotMessage(data.response, data.attachments);
            
            // Sohbet listesini güncelle
            updateSidebarChats();

        } catch (error) {
            loadingMessage.remove();
            console.error('Mesaj gönderilirken hata:', error);
            showError('Mesaj gönderilemedi. Lütfen tekrar deneyin.');
        }
    });

    // Örnek soru butonları
    document.querySelectorAll('.query-btn').forEach(button => {
        button.addEventListener('click', () => {
            messageInput.value = button.textContent.trim();
            messageInput.focus();
        });
    });

    // Yardımcı fonksiyonlar
    function addUserMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message user-message';
        messageDiv.innerHTML = `<p>${escapeHtml(message)}</p>`;
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    function addBotMessage(message, attachments = null) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message bot-message';
        
        let attachmentsHtml = '';
        if (attachments) {
            attachmentsHtml = createAttachmentsHtml(attachments);
        }

        messageDiv.innerHTML = `
            <p>${markdownToHtml(message)}</p>
            ${attachmentsHtml}
        `;
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    function addLoadingMessage() {
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'message bot-message loading';
        loadingDiv.innerHTML = `
            <div class="typing-indicator">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;
        chatMessages.appendChild(loadingDiv);
        scrollToBottom();
        return loadingDiv;
    }

    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'message error-message';
        errorDiv.innerHTML = `<p>${escapeHtml(message)}</p>`;
        chatMessages.appendChild(errorDiv);
        scrollToBottom();

        // 5 saniye sonra hata mesajını kaldır
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }

    async function updateSidebarChats() {
        try {
            const response = await fetch('/api/chat/list');
            const chats = await response.json();
            
            const sidebarChats = document.querySelector('.sidebar-chats');
            sidebarChats.innerHTML = chats.map(chat => `
                <div class="chat-item ${chat.id === currentChatId ? 'active' : ''}" 
                     data-chat-id="${chat.id}">
                    <div class="chat-title">${escapeHtml(chat.title || 'Yeni Sohbet')}</div>
                    <div class="chat-actions">
                        <button class="btn btn-link btn-sm" onclick="exportChat(${chat.id})">
                            <i class="material-icons">download</i>
                        </button>
                        <button class="btn btn-link btn-sm" onclick="deleteChat(${chat.id})">
                            <i class="material-icons">delete</i>
                        </button>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Sohbet listesi güncellenirken hata:', error);
        }
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function markdownToHtml(text) {
        // Basit markdown dönüşümü
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`(.*?)`/g, '<code>$1</code>')
            .replace(/\n/g, '<br>');
    }

    function createAttachmentsHtml(attachments) {
        return attachments.map(attachment => {
            if (attachment.type === 'image') {
                return `<img src="${escapeHtml(attachment.url)}" alt="${escapeHtml(attachment.name)}" class="attachment-image">`;
            } else if (attachment.type === 'pdf') {
                return `<a href="${escapeHtml(attachment.url)}" target="_blank" class="attachment-link">
                    <i class="material-icons">description</i> ${escapeHtml(attachment.name)}
                </a>`;
            }
        }).join('');
    }
});