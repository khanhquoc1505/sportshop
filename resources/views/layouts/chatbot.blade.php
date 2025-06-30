{{-- resources/views/layouts/chatbot.blade.php --}}
<style>
  /* Overlay mờ */
  .modal-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    display: none;
    z-index: 1000;
  }

  /* Popup chat */
  .modal-chat {
    position: fixed;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 350px; height: 500px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 1001;
  }

  /* Header */
  #chat-header {
    background: #007bff; color: #fff;
    padding: 12px; font-weight: bold;
  }
  #chat-header #chat-close {
    float: right; cursor: pointer;
  }

  /* Body */
  #chat-body {
    flex: 1; padding: 10px;
    overflow-y: auto;
  }

  /* Footer */
  #chat-footer {
    padding: 10px; border-top: 1px solid #eee;
    display: flex;
  }
  #chat-footer input {
    flex: 1; padding: 6px; border: 1px solid #ccc;
    border-radius: 4px;
  }
  #chat-footer button {
    margin-left: 8px; padding: 6px 12px;
    background: #007bff; color: #fff;
    border: none; border-radius: 4px; cursor: pointer;
  }
</style>

{{-- Overlay --}}
<div class="modal-overlay"></div>

{{-- Popup chat --}}
<div class="modal-chat" id="chat-modal">
  <div id="chat-header">
    Hỗ trợ khách hàng
    <span id="chat-close">✕</span>
  </div>
  <div id="chat-body"></div>
  <div id="chat-footer">
    <input id="chat-input" type="text" placeholder="Nhập tin nhắn...">
    <button id="chat-send">Gửi</button>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const token    = document.querySelector('meta[name="csrf-token"]').content;
  const bubble   = document.querySelector('.chat-bubble');
  const overlay  = document.querySelector('.modal-overlay');
  const modal    = document.querySelector('.modal-chat');
  const closeBtn = document.getElementById('chat-close');
  const chatIn   = document.getElementById('chat-input');
  const chatSend = document.getElementById('chat-send');
  const chatBody = document.getElementById('chat-body');

  const openChat = () => {
    overlay.style.display = 'block';
    modal.style.display   = 'flex';
  };
  const closeChat = () => {
    overlay.style.display = 'none';
    modal.style.display   = 'none';
  };

  bubble.addEventListener('click', openChat);
  closeBtn.addEventListener('click', closeChat);
  overlay.addEventListener('click', closeChat);

  const sendMessage = async () => {
    const text = chatIn.value.trim();
    if (!text) return;

    // Hiển thị tin nhắn user
    const u = document.createElement('div');
    u.innerHTML = `<strong>Bạn:</strong> ${text}`;
    chatBody.appendChild(u);
    chatBody.scrollTop = chatBody.scrollHeight;
    chatIn.value = '';

    try {
      const res = await fetch('/support/ask', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({ question: text })
      });
      if (!res.ok) throw new Error(`Status ${res.status}`);
      const { reply } = await res.json();

      // Format reply: chuyển newline thành <br> để xuống dòng
      const b = document.createElement('div');
      const formatted = reply.replace(/\n/g, '<br>');
      b.innerHTML = `<strong>Bot:</strong><br>${formatted}`;
      chatBody.appendChild(b);
      chatBody.scrollTop = chatBody.scrollHeight;

    } catch (e) {
      console.error('Chat error:', e);
      const errDiv = document.createElement('div');
      errDiv.innerHTML = `<em>Lỗi kết nối, vui lòng thử lại sau.</em>`;
      chatBody.appendChild(errDiv);
      chatBody.scrollTop = chatBody.scrollHeight;
    }
  };

  chatSend.addEventListener('click', sendMessage);
  chatIn.addEventListener('keypress', e => {
    if (e.key === 'Enter') sendMessage();
  });
});
</script>
