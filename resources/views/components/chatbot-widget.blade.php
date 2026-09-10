<!-- Bella AI Chatbot Widget -->
<div id="chatbot-btn" onclick="toggleBellaChat()" style="
    position: fixed; bottom: 30px; right: 30px;
    background: #1a1a2e; color: #c9a96e;
    width: 58px; height: 58px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    border: 2px solid #c9a96e;
    z-index: 9999; transition: transform 0.2s, box-shadow 0.2s;">
    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
    </svg>
</div>

<div id="bella-chatbox" style="
    display: none; position: fixed; bottom: 100px; right: 30px;
    width: 360px; max-width: calc(100vw - 40px); background: white; border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.25);
    z-index: 9999; overflow: hidden; font-family: 'Poppins', Arial, sans-serif;
    border: 1px solid #e8e8e8;">

    <!-- Header -->
    <div style="background: #1a1a2e; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #c9a96e;">
        <div>
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="color: white; font-weight: 700; font-size: 16px; font-family: 'Playfair Display', serif;">Bella</span>
                <span style="background: #c9a96e; color: #1a1a2e; font-size: 9px; font-weight: 800; letter-spacing: 0.5px; padding: 2px 7px; border-radius: 10px; text-transform: uppercase;">AI</span>
            </div>
            <span style="color: rgba(255,255,255,0.7); font-size: 11px;">Beauty Portal Smart Assistant</span>
        </div>
        <span onclick="toggleBellaChat()" style="color: white; cursor: pointer; font-size: 22px; line-height: 1; opacity: 0.8;">&times;</span>
    </div>

    <!-- Messages Container -->
    <div id="bella-messages" style="
        height: 330px; overflow-y: auto;
        padding: 16px; background: #fafafa; display: flex; flex-direction: column; gap: 10px;">
        <div style="background: white; color: #2c2c2c; padding: 12px 16px; border-radius: 14px 14px 14px 0; font-size: 13px; max-width: 88%; border-left: 3px solid #c9a96e; box-shadow: 0 1px 4px rgba(0,0,0,0.06); line-height: 1.45;">
            Hi! I'm Bella, your AI beauty assistant. Ask me about available services, beauticians, pricing, or how to book — you can also send a photo or use your voice!
        </div>
    </div>

    <!-- Image Preview Bar -->
    <div id="bella-image-preview" style="display:none; padding: 8px 14px; background: white; border-top: 1px solid #eee; align-items: center; gap: 10px;">
        <img id="bella-thumb" src="" style="width: 38px; height: 38px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
        <span style="font-size: 12px; color: #666; flex: 1;">Photo attached</span>
        <span onclick="removeBellaImage()" style="cursor: pointer; color: #c62828; font-size: 18px; line-height: 1; font-weight: bold;">&times;</span>
    </div>

    <!-- Input Bar -->
    <div style="padding: 12px; background: white; display: flex; gap: 8px; border-top: 1px solid #eee; align-items: center;">
        <input type="file" id="bella-file-input" accept="image/*" style="display:none;" onchange="handleBellaImage(event)">

        <button type="button" onclick="document.getElementById('bella-file-input').click()" title="Attach Photo" style="
            background: none; border: none; color: #777; cursor: pointer;
            width: 36px; height: 36px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
            border-radius: 50%; padding: 0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
            </svg>
        </button>

        <button type="button" id="bella-mic-btn" onclick="toggleBellaVoice()" title="Voice input" style="
            background: none; border: none; color: #777; cursor: pointer;
            width: 36px; height: 36px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
            border-radius: 50%; padding: 0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                <line x1="12" y1="19" x2="12" y2="23"></line>
                <line x1="8" y1="23" x2="16" y2="23"></line>
            </svg>
        </button>

        <input type="text" id="bella-input" placeholder="Ask Bella anything..." onkeypress="handleBellaKeyPress(event)" style="
            flex: 1; padding: 10px 14px; border: 1px solid #ddd; border-radius: 22px;
            font-size: 13px; font-family: inherit; outline: none;">

        <button type="button" onclick="sendBellaMessage()" id="bella-send-btn" style="
            background: #1a1a2e; color: #c9a96e; border: 1px solid #c9a96e;
            width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center;
            justify-content: center; cursor: pointer; flex-shrink: 0;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"></line>
                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
        </button>
    </div>
</div>

<script>
let bellaChatOpen = false;
let bellaHistory = [];
let bellaSelectedImage = null;
let bellaImageMime = 'image/jpeg';
let bellaSpeechRecognizer = null;
let bellaIsListening = false;

function toggleBellaChat() {
    const box = document.getElementById('bella-chatbox');
    bellaChatOpen = !bellaChatOpen;
    box.style.display = bellaChatOpen ? 'block' : 'none';
    if (bellaChatOpen) {
        document.getElementById('bella-input').focus();
    }
}

function handleBellaKeyPress(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        sendBellaMessage();
    }
}

function handleBellaImage(e) {
    const file = e.target.files[0];
    if (!file) return;

    bellaImageMime = file.type || 'image/jpeg';
    const reader = new FileReader();
    reader.onload = function(evt) {
        const fullBase64 = evt.target.result;
        bellaSelectedImage = fullBase64.split(',')[1];
        document.getElementById('bella-thumb').src = fullBase64;
        document.getElementById('bella-image-preview').style.display = 'flex';
    };
    reader.readAsDataURL(file);
}

function removeBellaImage() {
    bellaSelectedImage = null;
    document.getElementById('bella-file-input').value = '';
    document.getElementById('bella-image-preview').style.display = 'none';
}

function toggleBellaVoice() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
        alert("Voice recognition is not supported on this browser. Try Chrome or Edge!");
        return;
    }

    const micBtn = document.getElementById('bella-mic-btn');

    if (bellaIsListening) {
        if (bellaSpeechRecognizer) bellaSpeechRecognizer.stop();
        bellaIsListening = false;
        micBtn.style.color = '#777';
        return;
    }

    bellaSpeechRecognizer = new SpeechRecognition();
    bellaSpeechRecognizer.continuous = false;
    bellaSpeechRecognizer.interimResults = false;

    bellaSpeechRecognizer.onstart = function() {
        bellaIsListening = true;
        micBtn.style.color = '#c62828';
    };

    bellaSpeechRecognizer.onresult = function(e) {
        const transcript = e.results[0][0].transcript;
        const input = document.getElementById('bella-input');
        input.value = (input.value ? input.value + ' ' : '') + transcript;
        bellaIsListening = false;
        micBtn.style.color = '#777';
    };

    bellaSpeechRecognizer.onerror = function() {
        bellaIsListening = false;
        micBtn.style.color = '#777';
    };

    bellaSpeechRecognizer.onend = function() {
        bellaIsListening = false;
        micBtn.style.color = '#777';
    };

    bellaSpeechRecognizer.start();
}

function formatBellaText(text) {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/\n/g, '<br>');
}

function sendBellaMessage() {
    const input = document.getElementById('bella-input');
    const msg = input.value.trim();

    if (!msg && !bellaSelectedImage) return;

    const msgsContainer = document.getElementById('bella-messages');

    // Display user message bubble
    let userHtml = '';
    if (bellaSelectedImage) {
        userHtml += `<img src="data:${bellaImageMime};base64,${bellaSelectedImage}" style="max-width: 140px; border-radius: 8px; margin-bottom: 6px; display: block;">`;
    }
    if (msg) {
        userHtml += `<div>${formatBellaText(msg)}</div>`;
    }

    const userDiv = document.createElement('div');
    userDiv.style.cssText = 'background: #1a1a2e; color: white; padding: 10px 14px; border-radius: 14px 14px 0 14px; font-size: 13px; max-width: 85%; align-self: flex-end; box-shadow: 0 1px 4px rgba(0,0,0,0.08);';
    userDiv.innerHTML = userHtml;
    msgsContainer.appendChild(userDiv);

    // Typing indicator
    const typingDiv = document.createElement('div');
    typingDiv.id = 'bella-typing-indicator';
    typingDiv.style.cssText = 'background: white; color: #999; padding: 10px 14px; border-radius: 14px 14px 14px 0; font-size: 12px; font-style: italic; max-width: 70%; border-left: 3px solid #c9a96e; box-shadow: 0 1px 4px rgba(0,0,0,0.06);';
    typingDiv.innerText = 'Bella is typing...';
    msgsContainer.appendChild(typingDiv);
    msgsContainer.scrollTop = msgsContainer.scrollHeight;

    const currentMsg = msg;
    const currentImg = bellaSelectedImage;
    const currentMime = bellaImageMime;

    // Reset input fields
    input.value = '';
    removeBellaImage();

    // Call Laravel endpoint
    fetch("{{ route('chatbot.message') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            message: currentMsg,
            image: currentImg,
            image_mime: currentMime,
            history: JSON.stringify(bellaHistory)
        })
    })
    .then(res => res.json())
    .then(data => {
        const ind = document.getElementById('bella-typing-indicator');
        if (ind) ind.remove();

        const reply = data.reply || "Sorry, I couldn't process that.";

        const botDiv = document.createElement('div');
        botDiv.style.cssText = 'background: white; color: #2c2c2c; padding: 12px 16px; border-radius: 14px 14px 14px 0; font-size: 13px; max-width: 88%; border-left: 3px solid #c9a96e; box-shadow: 0 1px 4px rgba(0,0,0,0.06); line-height: 1.45;';
        botDiv.innerHTML = formatBellaText(reply);
        msgsContainer.appendChild(botDiv);
        msgsContainer.scrollTop = msgsContainer.scrollHeight;

        // Append to history
        if (currentMsg) {
            bellaHistory.push({ role: 'user', text: currentMsg });
            bellaHistory.push({ role: 'model', text: reply });
        }
    })
    .catch(err => {
        const ind = document.getElementById('bella-typing-indicator');
        if (ind) ind.remove();

        const botDiv = document.createElement('div');
        botDiv.style.cssText = 'background: #ffebee; color: #c62828; padding: 10px 14px; border-radius: 14px; font-size: 13px; max-width: 88%;';
        botDiv.innerText = "Connection error. Please try again.";
        msgsContainer.appendChild(botDiv);
        msgsContainer.scrollTop = msgsContainer.scrollHeight;
    });
}
</script>
