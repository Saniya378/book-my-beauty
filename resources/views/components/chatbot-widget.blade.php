{{-- Bella AI assistant: floating launcher plus the chat panel --}}
<div class="bella">
    <div id="bella-chatbox" class="bella__panel" aria-label="Bella AI assistant">
        <div class="bella__head">
            <span class="bella__avatar"><x-icon name="sparkle" :size="18" /></span>

            <div>
                <div class="bella__name">Bella AI</div>
                <div class="bella__status">Online &middot; replies instantly</div>
            </div>

            <button type="button" class="bella__close" onclick="toggleBellaChat()" aria-label="Close chat">
                <x-icon name="close" :size="18" />
            </button>
        </div>

        <div class="bella__chips">
            <button type="button" class="bella__chip" onclick="sendQuickPrompt('What services and prices are available?')">
                Services &amp; prices
            </button>
            <button type="button" class="bella__chip" onclick="sendQuickPrompt('How do I book an appointment?')">
                How to book
            </button>
            <button type="button" class="bella__chip" onclick="sendQuickPrompt('Show me top rated beauticians')">
                Top beauticians
            </button>
        </div>

        <div id="bella-messages" class="bella__log">
            <div class="bella__msg bella__msg--bot">Hi, I am Bella, the BookMyBeauty assistant. Ask me about services, prices, opening hours or how to book an appointment.
            </div>
        </div>

        <div id="bella-voice-status" class="bella__voice">
            <span>
                <span class="bella__rec-dot"></span>
                <strong id="bella-rec-timer">00:00</strong>
                <span>Listening, speak your question</span>
            </span>
            <button type="button" class="btn btn--sm btn--ghost" onclick="stopAndTranscribeVoice()">Transcribe</button>
        </div>

        <div id="bella-image-preview" class="bella__attach">
            <img id="bella-thumb" src="" alt="Attached photo">
            <span>Photo attached to your question</span>
            <button type="button" onclick="removeBellaImage()" aria-label="Remove photo">
                <x-icon name="close" :size="16" />
            </button>
        </div>

        <div class="bella__form">
            <input type="file" id="bella-file-input" accept="image/*" hidden onchange="handleBellaImage(event)">

            <div class="bella__tools">
                <button type="button" class="bella__tool" title="Attach a photo"
                    onclick="document.getElementById('bella-file-input').click()">
                    <x-icon name="camera" :size="17" />
                </button>

                <button type="button" id="bella-mic-btn" class="bella__tool" title="Speak your question"
                    onclick="toggleBellaVoice()">
                    <x-icon name="mic" :size="17" />
                </button>
            </div>

            <input type="text" id="bella-input" placeholder="Type your question..." onkeypress="handleBellaKeyPress(event)">

            <button type="button" id="bella-send-btn" onclick="sendBellaMessage()" aria-label="Send message">
                <x-icon name="send" :size="16" />
            </button>
        </div>
    </div>

    <button type="button" class="bella__toggle" onclick="toggleBellaChat()">
        <span><x-icon name="message" :size="16" /></span>
        Ask Bella
    </button>
</div>

<script>
    const bella = {
        panel: document.getElementById('bella-chatbox'),
        log: document.getElementById('bella-messages'),
        input: document.getElementById('bella-input'),
        micButton: document.getElementById('bella-mic-btn'),
        sendButton: document.getElementById('bella-send-btn'),
        voiceBanner: document.getElementById('bella-voice-status'),
        voiceTimer: document.getElementById('bella-rec-timer'),
        attachment: document.getElementById('bella-image-preview'),
        fileInput: document.getElementById('bella-file-input'),

        history: [],
        image: null,
        imageMime: 'image/jpeg',

        recorder: null,
        chunks: [],
        stream: null,
        recording: false,
        seconds: 0,
        clock: null,
        speech: null,
        speechFilled: false
    };

    const IDLE_PLACEHOLDER = 'Type your question...';

    function toggleBellaChat() {
        const open = bella.panel.classList.toggle('is-open');

        if (open) {
            setTimeout(() => bella.input.focus(), 60);
        }
    }

    function sendQuickPrompt(text) {
        bella.input.value = text;
        sendBellaMessage();
    }

    function handleBellaKeyPress(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            sendBellaMessage();
        }
    }

    function scrollBellaToBottom() {
        bella.log.scrollTop = bella.log.scrollHeight;
    }

    function appendBellaMessage(role, html) {
        const bubble = document.createElement('div');
        bubble.className = role === 'user' ? 'bella__msg bella__msg--user' : 'bella__msg bella__msg--bot';
        bubble.innerHTML = html;
        bella.log.appendChild(bubble);
        scrollBellaToBottom();

        return bubble;
    }

    function formatBellaText(text) {
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');
    }

    /* ---------- photo attachment ---------- */

    function handleBellaImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        bella.imageMime = file.type || 'image/jpeg';

        const reader = new FileReader();
        reader.onload = (loadEvent) => {
            bella.image = loadEvent.target.result.split(',')[1];
            document.getElementById('bella-thumb').src = loadEvent.target.result;
            bella.attachment.style.display = 'flex';
        };
        reader.readAsDataURL(file);
    }

    function removeBellaImage() {
        bella.image = null;
        bella.fileInput.value = '';
        bella.attachment.style.display = 'none';
    }

    /* ---------- voice input ---------- */

    function supportedRecordingType() {
        if (!window.MediaRecorder || !MediaRecorder.isTypeSupported) return '';

        return ['audio/webm', 'audio/mp4', 'audio/ogg'].find(type => MediaRecorder.isTypeSupported(type)) || '';
    }

    function startBellaClock() {
        bella.seconds = 0;
        bella.voiceTimer.textContent = '00:00';

        bella.clock = setInterval(() => {
            bella.seconds++;
            const minutes = String(Math.floor(bella.seconds / 60)).padStart(2, '0');
            bella.voiceTimer.textContent = minutes + ':' + String(bella.seconds % 60).padStart(2, '0');
        }, 1000);
    }

    function stopBellaClock() {
        if (bella.clock) {
            clearInterval(bella.clock);
            bella.clock = null;
        }
    }

    function setRecordingUi(active) {
        bella.recording = active;
        bella.micButton.classList.toggle('is-recording', active);
        bella.voiceBanner.style.display = active ? 'flex' : 'none';
        bella.input.placeholder = active ? 'Listening, speak now' : IDLE_PLACEHOLDER;
    }

    async function toggleBellaVoice() {
        if (bella.recording) {
            stopAndTranscribeVoice();
            return;
        }

        try {
            bella.stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            bella.chunks = [];
            bella.speechFilled = false;

            const mimeType = supportedRecordingType();
            bella.recorder = mimeType ? new MediaRecorder(bella.stream, { mimeType }) : new MediaRecorder(bella.stream);

            bella.recorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    bella.chunks.push(event.data);
                }
            };

            bella.recorder.onstart = () => {
                setRecordingUi(true);
                startBellaClock();
            };

            bella.recorder.start(200);
            listenWithWebSpeech();
        } catch (error) {
            alert('Microphone access is unavailable. Please allow microphone permission and try again.');
        }
    }

    /* Where the browser supports it we get the text instantly; otherwise the
       recorded clip is sent to the transcription endpoint. */
    function listenWithWebSpeech() {
        const Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!Recognition) return;

        try {
            bella.speech = new Recognition();
            bella.speech.continuous = false;
            bella.speech.interimResults = true;
            bella.speech.lang = 'ur-PK';

            bella.speech.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                const isDevanagari = /[\u0900-\u097F]/.test(transcript);

                if (transcript && !isDevanagari) {
                    bella.speechFilled = true;
                    bella.input.value = transcript;
                }
            };

            bella.speech.start();
        } catch (error) {
            bella.speech = null;
        }
    }

    function stopAndTranscribeVoice() {
        if (!bella.recording || !bella.recorder) return;

        if (bella.speech) {
            try {
                bella.speech.stop();
            } catch (error) {
                // recognition already stopped
            }
        }

        stopBellaClock();
        setRecordingUi(false);

        bella.recorder.onstop = () => {
            if (bella.stream) {
                bella.stream.getTracks().forEach(track => track.stop());
            }

            const audio = new Blob(bella.chunks, { type: bella.recorder.mimeType || 'audio/webm' });

            if (audio.size < 400) return;

            if (bella.speechFilled && bella.input.value.trim()) {
                bella.input.focus();
                return;
            }

            bella.input.value = 'Transcribing...';

            const reader = new FileReader();
            reader.onload = (loadEvent) => {
                fetch(@json(route('chatbot.transcribe')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token())
                    },
                    body: JSON.stringify({
                        audio: loadEvent.target.result.split(',')[1],
                        audio_mime: bella.recorder.mimeType || 'audio/webm'
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        bella.input.value = data.text ? data.text.trim() : '';
                        bella.input.focus();
                    })
                    .catch(() => {
                        bella.input.value = '';
                        bella.input.focus();
                    });
            };

            reader.readAsDataURL(audio);
        };

        try {
            bella.recorder.stop();
        } catch (error) {
            // recorder already stopped
        }
    }

    /* ---------- sending ---------- */

    function sendBellaMessage() {
        if (bella.recording) {
            stopAndTranscribeVoice();
            return;
        }

        const text = bella.input.value.trim();
        if (!text && !bella.image) return;

        let outgoing = '';
        if (bella.image) {
            outgoing += `<img class="bella__photo" src="data:${bella.imageMime};base64,${bella.image}">`;
        }
        if (text) {
            outgoing += `<div>${formatBellaText(text)}</div>`;
        }

        appendBellaMessage('user', outgoing);

        const typing = document.createElement('div');
        typing.id = 'bella-typing';
        typing.className = 'bella__msg bella__msg--bot bella__msg--pending';
        typing.innerHTML = 'Bella is thinking <span class="bella__dots"><span></span><span></span><span></span></span>';
        bella.log.appendChild(typing);
        scrollBellaToBottom();

        const payload = {
            message: text,
            image: bella.image,
            image_mime: bella.imageMime,
            history: JSON.stringify(bella.history.slice(-6))
        };

        bella.input.value = '';
        removeBellaImage();
        bella.sendButton.classList.add('is-busy');

        fetch(@json(route('chatbot.message')), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token())
            },
            body: JSON.stringify(payload)
        })
            .then(response => response.json())
            .then(data => {
                document.getElementById('bella-typing')?.remove();

                const reply = data.reply || 'Sorry, I could not process that. Please try again.';

                if (payload.message) {
                    bella.history.push({ role: 'user', text: payload.message });
                    bella.history.push({ role: 'model', text: reply });
                }

                appendBellaMessage('bot', formatBellaText(reply));
            })
            .catch(() => {
                document.getElementById('bella-typing')?.remove();

                const failure = appendBellaMessage('bot', 'Connection problem. Please check your network and try again.');
                failure.classList.add('bella__msg--error');
            })
            .finally(() => bella.sendButton.classList.remove('is-busy'));
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            bella.panel.classList.remove('is-open');
        }
    });
</script>