@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold">Data Assistant ChatBot</h2>
        <p class="text-muted small mb-0">Ask real-time analytical questions regarding your NinjaVan datasets and customer reviews.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">
            {{-- Chat Card Shell --}}
            <div class="card shadow-sm border-0 d-flex flex-column" style="height: calc(100vh - 220px); min-height: 480px;">
                
                {{-- Chat Box Header --}}
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            <i class="bi bi-robot fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark mb-0">NinjaBot v1.0</div>
                            <div class="text-success small d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                <span class="d-inline-block bg-success rounded-circle" style="width: 6px; height: 6px;"></span> Core Database Connected
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary border-0" onclick="clearChat()" title="Reset Session">
                        <i class="bi bi-trash3 me-1"></i> Clear Chat
                    </button>
                </div>

                {{-- Scrollable Conversation Stream Area --}}
                <div class="card-body bg-light overflow-auto p-4 flex-grow-1" id="chatArea" style="background-color: #fdfdfd !important;">
                    
                    {{-- Bot Default Message --}}
                    <div class="d-flex mb-3 gap-3">
                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                            <i class="bi bi-robot"></i>
                        </div>
                        <div class="p-3 bg-white shadow-sm rounded-3 border text-dark message-box" style="max-width: 75%;">
                            Hi! I am your tracking and fulfillment data assistant. Ask me questions like:
                            <ul class="mb-0 mt-2 ps-3 text-muted small">
                                <li><em>"How many parcels do we have total?"</em></li>
                                <li><em>"What is our average weight?"</em></li>
                                <li><em>"What are our satisfaction scores?"</em></li>
                            </ul>
                        </div>
                    </div>

                </div>

                {{-- Chat Input Form Area --}}
                <div class="card-footer bg-white border-top p-3">
                    <form id="chatForm" autocomplete="off" onsubmit="sendMessage(event)">
                        @csrf
                        <div class="input-group">
                            <input type="text" id="userInput" class="form-control border shadow-none py-2 px-3" placeholder="Type your data query here (e.g., 'What is our total parcel count?')..." required>
                            <button type="submit" class="btn btn-danger px-4 d-flex align-items-center gap-2">
                                <span>Send</span> <i class="bi bi-send-fill small"></i>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Custom Style Overrides for Chat Layout formatting --}}
<style>
    .message-box ul { margin-top: 5px; }
    .message-box p { margin-bottom: 0px; }
</style>

<script>
    const chatArea = document.getElementById('chatArea');
    const userInput = document.getElementById('userInput');

    function appendMessage(sender, text) {
        const isBot = (sender === 'bot');
        const messageRow = document.createElement('div');
        messageRow.className = `d-flex mb-3 gap-3 ${isBot ? '' : 'justify-content-end'}`;

        // Format system text returns nicely (handles line breaks and basic bolding markers)
        let formattedText = text.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

        const avatar = isBot 
            ? `<div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;"><i class="bi bi-robot"></i></div>` 
            : '';

        const bubble = `
            <div class="p-3 shadow-sm rounded-3 message-box ${isBot ? 'bg-white border text-dark' : 'bg-danger text-white'}" style="max-width: 75%; white-space: pre-line;">
                ${formattedText}
            </div>
        `;

        messageRow.innerHTML = isBot ? (avatar + bubble) : (bubble + avatar);
        chatArea.appendChild(messageRow);
        chatArea.scrollTop = chatArea.scrollHeight;
    }

    function sendMessage(e) {
        e.preventDefault();
        const text = userInput.value.trim();
        if (!text) return;

        // 1. Show user message instantly in view layout
        appendMessage('user', text);
        userInput.value = '';

        // 2. Show a clean placeholder loading typing indicator bubble
        const typingId = 'typing-' + Date.now();
        const typingRow = document.createElement('div');
        typingRow.id = typingId;
        typingRow.className = 'd-flex mb-3 gap-3';
        typingRow.innerHTML = `
            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;"><i class="bi bi-robot"></i></div>
            <div class="p-3 bg-white border text-muted rounded-3 shadow-sm text-center small py-2">
                <span class="spinner-border spinner-border-sm me-1 text-danger" role="status"></span> Thinking...
            </div>
        `;
        chatArea.appendChild(typingRow);
        chatArea.scrollTop = chatArea.scrollHeight;

        // 3. Dispatch AJAX Request securely to backend controller endpoint
        fetch("{{ route('chatbot.message') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById(typingId).remove();
            appendMessage('bot', data.reply);
        })
        .catch(err => {
            document.getElementById(typingId).remove();
            appendMessage('bot', "Sorry, I ran into an internal system error trying to fetch that database record.");
            console.error(err);
        });
    }

    function clearChat() {
        chatArea.innerHTML = `
            <div class="d-flex mb-3 gap-3">
                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;"><i class="bi bi-robot"></i></div>
                <div class="p-3 bg-white shadow-sm rounded-3 border text-dark message-box" style="max-width: 75%;">
                    Session reset. What else can I calculate from the shipping logs for you today?
                </div>
            </div>
        `;
    }
</script>
@endsection