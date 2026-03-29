<?php View::include('layouts/app', $data = [
    'title' => 'Trò chuyện cùng AI',
    'content' => (function() { ob_start(); ?>

<div class="chatbot-container">
    <div class="chatbot-window">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="bot-info">
                <div class="bot-avatar"><i class="fas fa-robot"></i></div>
                <div class="bot-status">
                    <h3>Trợ lý Ảo ClothingShop</h3>
                    <p><span class="status-dot"></span> Đang trực tuyến</p>
                </div>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="chat-messages" class="chat-messages">
            <div class="message bot">
                <div class="message-content"> Chào bạn! Tôi là trợ lý ảo của ClothingShop. Tôi có thể giúp gì cho bạn?</div>
            </div>
        </div>

        <div class="chatbot-quick-replies">
            <button type="button" class="quick-reply">Sản phẩm cửa hàng</button>
            <button type="button" class="quick-reply">Tư vấn</button>
            <button type="button" class="quick-reply">Các bước đặt hàng</button>
        </div>

        <!-- Input Area -->
        <div class="chat-input-area">
            <form id="chat-form">
                <input type="text" id="user-input" placeholder="Nhập tin nhắn..." autocomplete="off">
                <button type="submit"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
</div>

<style>
.chatbot-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 0 15px;
    font-family: 'Inter', sans-serif;
}

.chatbot-window {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    height: 600px;
    overflow: hidden;
}

.chatbot-header {
    background: #252525;
    color: #fff;
    padding: 20px;
    display: flex;
    align-items: center;
}

.bot-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.bot-avatar {
    width: 45px;
    height: 45px;
    background: #4a4a4a;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.status-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #4cd137;
    border-radius: 50%;
    margin-right: 5px;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
    background: #f8f9fa;
}

.message {
    max-width: 80%;
}

.message.bot { align-self: flex-start; }
.message.user { align-self: flex-end; }

.message-content {
    padding: 12px 18px;
    border-radius: 18px;
    line-height: 1.5;
    font-size: 15px;
}

.message.bot .message-content {
    background: #fff;
    color: #333;
    border: 1px solid #eee;
    border-bottom-left-radius: 2px;
}

.message.user .message-content {
    background: #000;
    color: #fff;
    border-bottom-right-radius: 2px;
}

.chat-input-area {
    padding: 20px;
    background: #fff;
    border-top: 1px solid #eee;
}

#chat-form {
    display: flex;
    gap: 10px;
}

#user-input {
    flex: 1;
    padding: 12px 20px;
    border: 1px solid #ddd;
    border-radius: 25px;
    outline: none;
    transition: border-color 0.3s;
}

#user-input:focus { border-color: #000; }

#chat-form button {
    background: #000;
    color: #fff;
    border: none;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    cursor: pointer;
    transition: transform 0.2s;
}

#chat-form button:hover { transform: scale(1.05); }

.typing {
    font-style: italic;
    font-size: 12px;
    color: #888;
}

.chatbot-quick-replies {
    padding: 15px 20px;
    display: flex;
    gap: 10px;
    overflow-x: auto;
    background: #fff;
    border-top: 1px solid #eee;
    scrollbar-width: none;
}

.chatbot-quick-replies::-webkit-scrollbar {
    display: none;
}

.quick-reply {
    background: #f8f9fa;
    border: 1px solid #e1e1e1;
    color: #333;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.2s ease;
}

.quick-reply:hover {
    background: #000;
    color: #fff;
    border-color: #000;
}
</style>

<script>
document.getElementById('chat-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const input = document.getElementById('user-input');
    const message = input.value.trim();
    if (!message) return;

    input.value = '';
    appendMessage('user', message);

    const typingId = 'typing-' + Date.now();
    appendMessage('bot', '<span class="typing">Trợ lý đang trả lời...</span>', typingId);

    try {
        const formData = new FormData();
        formData.append('message', message);

        const response = await fetch('<?= Config::get("BASE_URL") ?>/chatbot/chat', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        const typingEl = document.getElementById(typingId);
        if (typingEl) {
            typingEl.querySelector('.message-content').innerHTML = result.reply || result.error;
        }
        } catch (error) {
        console.error(error);
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.querySelector('.message-content').innerText = 'Lỗi kết nối!';
    }
});

// Handle Quick Replies
document.querySelectorAll('.quick-reply').forEach(button => {
    button.addEventListener('click', () => {
        const input = document.getElementById('user-input');
        input.value = button.innerText;
        document.getElementById('chat-form').dispatchEvent(new Event('submit'));
    });
});

function appendMessage(role, text, id = null) {
    const container = document.getElementById('chat-messages');
    const div = document.createElement('div');
    div.className = `message ${role}`;
    if (id) div.id = id;
    div.innerHTML = `<div class="message-content">${text}</div>`;
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}
</script>

<?php return ob_get_clean(); })()
]); ?>
