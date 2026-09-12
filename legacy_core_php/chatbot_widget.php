<!-- Chatbot Widget -->
<div id="chatbot-btn" onclick="toggleChat()" style="
    position: fixed; bottom: 30px; right: 30px;
    background: var(--primary); color: var(--accent);
    width: 58px; height: 58px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    border: 2px solid var(--accent);
    z-index: 1000;">
    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
</div>

<div id="chatbox" style="
    display: none; position: fixed; bottom: 100px; right: 30px;
    width: 340px; background: white; border-radius: 15px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.25);
    z-index: 1000; overflow: hidden; font-family: 'Poppins', Arial, sans-serif;
    border: 1px solid var(--border);">

    <div style="background: var(--primary); padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--accent);">
        <div>
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="color: white; font-weight: bold; font-size: 15px; font-family: 'Playfair Display', serif;">Bella</span>
                <span style="background: var(--accent); color: var(--primary); font-size: 9px; font-weight: 700; letter-spacing: 0.5px; padding: 2px 7px; border-radius: 10px; text-transform: uppercase;">AI</span>
            </div>
            <span style="color: rgba(255,255,255,0.65); font-size: 10px;">BookMyBeauty Assistant</span>
        </div>
        <span onclick="toggleChat()" style="color: white; cursor: pointer; font-size: 18px; line-height: 1;">&times;</span>
    </div>

    <div id="chat-messages" style="
        height: 320px; overflow-y: auto;
        padding: 15px; background: var(--light-bg);">
        <div style="background: white; color: var(--text-dark); padding: 10px 15px; border-radius: 15px 15px 15px 0; margin-bottom: 10px; font-size: 13px; max-width: 88%; border-left: 3px solid var(--accent); box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
            Hi! I'm Bella, your AI beauty assistant. Ask me about services, beauticians, or how to book — you can also send a photo or use your voice.
        </div>
    </div>

    <div id="image-preview-bar" style="display:none; padding: 8px 12px; background: white; border-top: 1px solid #eee; align-items: center; gap: 8px;">
        <img id="image-preview-thumb" src="" style="width: 36px; height: 36px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border);">
        <span style="font-size: 11px; color: var(--text-gray); flex: 1;">Image attached</span>
        <span onclick="removeImage()" style="cursor: pointer; color: #c62828; font-size: 16px; line-height: 1;">&times;</span>
    </div>

    <div style="padding: 10px; background: white; display: flex; gap: 6px; border-top: 1px solid #eee; align-items: center;">
        <input type="file" id="chat-image-input" accept="image/*" style="display:none;" onchange="handleImageSelect(event)">

        <button type="button" onclick="document.getElementById('chat-image-input').click()" title="Attach image" style="
            background: none; border: none; color: var(--text-gray); cursor: pointer;
            width: 34px; height: 34px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
            border-radius: 50%; padding: 0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
        </button>

        <button type="button" id="mic-btn" onclick="toggleVoice()" title="Voice input" style="
            background: none; border: none; color: var(--text-gray); cursor: pointer;
            width: 34px; height: 34px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
            border-radius: 50%; padding: 0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>
        </button>

        <input type="text" id="chat-input" placeholder="Type a message..."
            onkeypress="if(event.key==='Enter') sendMessage()"
            style="flex: 1; padding: 8px 12px; border: 1px solid #ddd; border-radius: 20px; font-size: 13px; outline: none; margin-bottom: 0; width: auto;">

        <button type="button" onclick="sendMessage()" style="
            background: var(--primary); color: var(--accent); border: none;
            width: 34px; height: 34px; border-radius: 50%; cursor: pointer;
            flex-shrink: 0; display: flex; align-items: center; justify-content: center; padding: 0;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M2 21l21-9L2 3v7l15 2-15 2v7z"></path></svg>
        </button>
    </div>
</div>

<script>
let chatHistory = [];
let pendingImageBase64 = null;
let pendingImageMime = null;
let recognition = null;
let isListening = false;

function toggleChat() {
    const box = document.getElementById('chatbox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
}

function getChatbotBasePath() {
    return '<?php echo str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/") - 2); ?>';
}

function handleImageSelect(event) {
    const file = event.target.files[0];
    if (!file) return;
    if (!file.type.startsWith('image/')) {
        alert('Please select an image file.');
        return;
    }
    const reader = new FileReader();
    reader.onload = function(e) {
        const result = e.target.result;
        pendingImageMime = file.type;
        pendingImageBase64 = result.split(',')[1];
        document.getElementById('image-preview-thumb').src = result;
        document.getElementById('image-preview-bar').style.display = 'flex';
    };
    reader.readAsDataURL(file);
}

function removeImage() {
    pendingImageBase64 = null;
    pendingImageMime = null;
    document.getElementById('image-preview-bar').style.display = 'none';
    document.getElementById('chat-image-input').value = '';
}

function toggleVoice() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
        alert('Voice input is not supported in this browser. Try Chrome or Edge.');
        return;
    }

    const micBtn = document.getElementById('mic-btn');

    if (isListening && recognition) {
        recognition.stop();
        return;
    }

    recognition = new SpeechRecognition();
    recognition.lang = 'en-US';
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;

    recognition.onstart = function() {
        isListening = true;
        micBtn.style.color = '#c62828';
    };

    recognition.onresult = function(event) {
        const transcript = event.results[0][0].transcript;
        document.getElementById('chat-input').value = transcript;
    };

    recognition.onerror = function() {
        isListening = false;
        micBtn.style.color = 'var(--text-gray)';
    };

    recognition.onend = function() {
        isListening = false;
        micBtn.style.color = 'var(--text-gray)';
    };

    recognition.start();
}

function sendMessage() {
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    if (!message && !pendingImageBase64) return;

    addMessage(message || '(sent an image)', 'user');
    input.value = '';

    const imageToSend = pendingImageBase64;
    const imageMimeToSend = pendingImageMime;
    const hadImage = !!pendingImageBase64;
    removeImage();

    addMessage('...', 'bot', 'typing');

    const body = new URLSearchParams();
    body.append('message', message);
    body.append('history', JSON.stringify(chatHistory));
    if (imageToSend) {
        body.append('image', imageToSend);
        body.append('image_mime', imageMimeToSend);
    }

    fetch(getChatbotBasePath() + 'chatbot.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString()
    })
    .then(r => r.json())
    .then(data => {
        const typing = document.getElementById('typing');
        if (typing) typing.remove();
        addMessage(data.reply, 'bot');

        chatHistory.push({ role: 'user', text: message || (hadImage ? 'Sent an image.' : '') });
        chatHistory.push({ role: 'model', text: data.reply });
        if (chatHistory.length > 12) {
            chatHistory = chatHistory.slice(chatHistory.length - 12);
        }
    })
    .catch(() => {
        const typing = document.getElementById('typing');
        if (typing) typing.remove();
        addMessage('Sorry, something went wrong. Please try again.', 'bot');
    });
}

function addMessage(text, sender, id='') {
    const div = document.createElement('div');
    if(id) div.id = id;
    div.style.cssText = `
        padding: 10px 15px;
        border-radius: ${sender==='user' ? '15px 15px 0 15px' : '15px 15px 15px 0'};
        margin-bottom: 10px;
        font-size: 13px;
        max-width: 88%;
        white-space: pre-line;
        background: ${sender==='user' ? 'var(--primary)' : 'white'};
        color: ${sender==='user' ? 'white' : 'var(--text-dark)'};
        margin-left: ${sender==='user' ? 'auto' : '0'};
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border-left: ${sender==='user' ? 'none' : '3px solid var(--accent)'};
        display: block;
    `;
    div.textContent = text;
    document.getElementById('chat-messages').appendChild(div);
    document.getElementById('chat-messages').scrollTop = 999999;
}
</script>