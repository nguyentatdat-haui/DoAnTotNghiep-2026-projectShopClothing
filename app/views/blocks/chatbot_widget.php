<!-- Floating ChatBot Widget -->
<div id="chatbot-widget" class="chatbot-widget">
    <!-- Chat Toggle Button -->
    <button id="chatbot-toggle" class="chatbot-toggle" aria-label="Mở chat">
        <i class="fas fa-comment-dots"></i>
        <span class="notification-dot"></span>
    </button>

    <!-- Chat Window (Initial Hidden) -->
    <div id="chatbot-window" class="chatbot-window hidden">
        <div class="chatbot-header">
            <div class="bot-info">
                <div class="bot-avatar"><i class="fas fa-robot"></i></div>
                <div class="bot-status">
                    <h3>Trợ lý ClothingShop</h3>
                    <p><span class="status-dot"></span> Đang trực tuyến</p>
                </div>
            </div>
            <button id="chatbot-close" class="chatbot-close"><i class="fas fa-times"></i></button>
        </div>

        <div id="chatbot-messages" class="chatbot-messages">
            <div class="message bot">
                <div class="message-content">Chào bạn! Tôi có thể giúp gì cho bạn?</div>
            </div>
        </div>

        <div class="chatbot-quick-replies">
            <button type="button" class="quick-reply">Sản phẩm cửa hàng</button>
            <button type="button" class="quick-reply">Tư vấn</button>
            <button type="button" class="quick-reply">Các bước đặt hàng</button>
        </div>

        <div class="chatbot-input-area">
            <form id="chatbot-form">
                <input type="text" id="chatbot-user-input" placeholder="Nhập tin nhắn..." autocomplete="off">
                <button type="submit"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
</div>

<style>
.chatbot-widget {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9999;
    font-family: 'Inter', sans-serif;
}

.chatbot-toggle {
    width: 60px;
    height: 60px;
    background: #fbc531;
    color: #000;
    border: none;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.chatbot-toggle:hover {
    transform: scale(1.1);
}

.notification-dot {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 12px;
    height: 12px;
    background: #ff4757;
    border: 2px solid #fff;
    border-radius: 50%;
}

/* Chat Window */
.chatbot-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    height: 500px;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: all 0.3s ease;
    transform-origin: bottom right;
}

.chatbot-window.hidden {
    opacity: 0;
    transform: scale(0.8) translateY(20px);
    pointer-events: none;
}

.chatbot-header {
    background: #fbc531;
    color: #000;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.bot-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.bot-avatar {
    width: 35px;
    height: 35px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.bot-status h3 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
}

.bot-status p {
    margin: 0;
    font-size: 11px;
    color: #4cd137;
    display: flex;
    align-items: center;
}

.status-dot {
    width: 6px;
    height: 6px;
    background: #4cd137;
    border-radius: 50%;
    margin-right: 4px;
}

.chatbot-close {
    background: none;
    border: none;
    color: #000;
    opacity: 0.7;
    cursor: pointer;
    font-size: 18px;
}

.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    background: #fdfdfd;
}

.message {
    max-width: 90%;
    font-size: 14px;
    line-height: 1.4;
    display: flex;
    align-items: flex-end;
    gap: 8px;
}

.message.bot { 
    align-self: flex-start;
    flex-direction: row;
}

.message.user { 
    align-self: flex-end;
    flex-direction: row-reverse;
}

.msg-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
}

.bot .msg-avatar {
    background: #b8860b;
    color: #fff;
}

.user .msg-avatar {
    background: #ddd;
    color: #555;
}

.message-content {
    padding: 10px 14px;
    border-radius: 14px;
    white-space: pre-line;
}

.message.bot .message-content {
    background: #f1f1f1;
    color: #333;
    border-bottom-left-radius: 2px;
}

.message.user .message-content {
    background: #fbc531;
    color: #000;
    border-bottom-right-radius: 2px;
}

.chatbot-input-area {
    padding: 15px;
    border-top: 1px solid #eee;
}

#chatbot-form {
    display: flex;
    gap: 8px;
}

#chatbot-user-input {
    flex: 1;
    border: 1px solid #eee;
    padding: 10px 15px;
    border-radius: 20px;
    outline: none;
    font-size: 14px;
}

#chatbot-form button {
    background: #fbc531;
    color: #000;
    border: none;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    cursor: pointer;
}

.typing-dot {
    font-style: italic;
    color: #999;
}

.chatbot-quick-replies {
    padding: 10px 15px;
    display: flex;
    gap: 8px;
    overflow-x: auto;
    background: #fff;
    border-top: 1px solid #eee;
    scrollbar-width: none; /* Firefox */
}

.chatbot-quick-replies::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Edge */
}

.quick-reply {
    background: #f8f9fa;
    border: 1px solid #e1e1e1;
    color: #555;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 12px;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.2s;
}

.quick-reply:hover {
    background: #fbc531;
    color: #000;
    border-color: #fbc531;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('chatbot-toggle');
    const close = document.getElementById('chatbot-close');
    const window = document.getElementById('chatbot-window');
    const form = document.getElementById('chatbot-form');
    const input = document.getElementById('chatbot-user-input');
    const messages = document.getElementById('chatbot-messages');

    // Toggle Chat Window
    toggle.addEventListener('click', () => {
        window.classList.toggle('hidden');
        document.querySelector('.notification-dot').style.display = 'none';
    });

    close.addEventListener('click', () => {
        window.classList.add('hidden');
    });

    // Handle Chat
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = input.value.trim();
        if (!msg) return;

        input.value = '';
        appendMessage('user', msg);

        const loadingId = 'loading-' + Date.now();
        appendMessage('bot', '<span class="typing-dot">Đang trả lời...</span>', loadingId);

        try {
            const formData = new FormData();
            formData.append('message', msg);

            const res = await fetch('<?= Config::get("BASE_URL") ?>/chatbot/chat', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            const botMsg = data.reply || data.error || 'Dữ liệu không hợp lệ từ máy chủ.';
            
            document.getElementById(loadingId).querySelector('.message-content').innerHTML = botMsg;
        } catch (err) {
            console.error('Chat Error:', err);
            document.getElementById(loadingId).querySelector('.message-content').innerText = 'Lỗi hệ thống: ' + (err.message || 'Không rõ nguyên nhân');
        }
    });

    // Handle Quick Replies
    document.querySelectorAll('.quick-reply').forEach(button => {
        button.addEventListener('click', () => {
            input.value = button.innerText;
            form.dispatchEvent(new Event('submit'));
        });
    });

    function appendMessage(role, text, id = null) {
        const div = document.createElement('div');
        div.className = `message ${role}`;
        if (id) div.id = id;
        
        const avatarIcon = role === 'bot' ? 'fa-robot' : 'fa-user';
        const avatarHtml = `<div class="msg-avatar"><i class="fas ${avatarIcon}"></i></div>`;
        
        div.innerHTML = `${avatarHtml}<div class="message-content">${text}</div>`;
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
    }
});
</script>
