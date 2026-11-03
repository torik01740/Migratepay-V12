<?php
    $aiPrimaryColor = \App\Models\AiSetting::getVal('ai_widget_color', '#3b82f6');
    $aiTitle = \App\Models\AiSetting::getVal('ai_widget_title', 'MigratePay Support');
    $aiWelcome = \App\Models\AiSetting::getVal('ai_widget_welcome', "Hello! 👋 I'm the MigratePay Support Assistant. How can I help you today?");
    $aiPosition = \App\Models\AiSetting::getVal('ai_widget_position', 'bottom-right');
    $aiLauncherIcon = \App\Models\AiSetting::getVal('ai_launcher_icon', '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>');
    $aiAvatarIcon = \App\Models\AiSetting::getVal('ai_avatar_icon', '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a2 2 0 0 1 2 2v2h4a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-4v2a2 2 0 0 1-4 0v-2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4V4a2 2 0 0 1 2-2zM8 10a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm8 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/></svg>');
    
    // Ensure fallback if empty string was saved
    if(!trim($aiLauncherIcon)) $aiLauncherIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>';
    if(!trim($aiAvatarIcon)) $aiAvatarIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a2 2 0 0 1 2 2v2h4a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-4v2a2 2 0 0 1-4 0v-2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4V4a2 2 0 0 1 2-2zM8 10a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm8 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/></svg>';
?>
<style>
    /* AI Chat Widget Styles */
    :root {
        --ai-primary: {{ $aiPrimaryColor }};
        --ai-primary-hover: #2563eb;
        --ai-bg: #f8fafc;
        --ai-text: #1e293b;
        --ai-light: #ffffff;
        --ai-border: #e2e8f0;
        --ai-gradient: linear-gradient(135deg, {{ $aiPrimaryColor }} 0%, #6366f1 100%);
    }

        #ai-chat-widget {
        position: fixed;
        bottom: 24px;
        {{ $aiPosition === 'bottom-left' ? 'left: 24px;' : 'right: 24px;' }}
        z-index: 9999;
        font-family: 'Inter', sans-serif;
    }

    /* Launcher Button */
    .ai-chat-launcher {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--ai-gradient);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border: none;
        outline: none;
    }

    .ai-chat-launcher:hover {
        transform: scale(1.05) translateY(-2px);
        box-shadow: 0 15px 30px rgba(59, 130, 246, 0.5);
    }

    .ai-chat-launcher svg {
        color: white;
        width: 28px;
        height: 28px;
        transition: transform 0.3s;
    }

    /* Chat Window */
    .ai-chat-window {
        position: absolute;
        bottom: 80px;
        right: 0;
        width: 380px;
        height: 600px;
        max-height: calc(100vh - 120px);
        background: var(--ai-light);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        opacity: 0;
        transform: translateY(20px) scale(0.95);
        pointer-events: none;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        transform-origin: bottom right;
    }

    .ai-chat-window.active {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: all;
    }

    /* Header */
    .ai-chat-header {
        background: var(--ai-gradient);
        padding: 20px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ai-chat-header-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ai-chat-avatar {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ai-chat-title {
        font-weight: 700;
        font-size: 16px;
        line-height: 1.2;
    }

    .ai-chat-status {
        font-size: 12px;
        opacity: 0.9;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .ai-chat-status::before {
        content: '';
        display: block;
        width: 6px;
        height: 6px;
        background: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
    }

    .ai-chat-close {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.2s;
        padding: 4px;
    }
    .ai-chat-close:hover { opacity: 1; }

    /* Messages Area */
    .ai-chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .ai-message {
        max-width: 85%;
        display: flex;
        flex-direction: column;
        animation: ai-fade-in 0.3s ease-out forwards;
    }

    @keyframes ai-fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .ai-message.ai {
        align-self: flex-start;
    }

    .ai-message.user {
        align-self: flex-end;
    }

    .ai-message-bubble {
        padding: 12px 16px;
        border-radius: 18px;
        font-size: 14px;
        line-height: 1.5;
        color: var(--ai-text);
        position: relative;
    }

    .ai-message.ai .ai-message-bubble {
        background: white;
        border: 1px solid var(--ai-border);
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }

    .ai-message.user .ai-message-bubble {
        background: var(--ai-gradient);
        color: white;
        border-bottom-right-radius: 4px;
        box-shadow: 0 4px 10px rgba(59,130,246,0.2);
    }

    .ai-message-label {
        font-size: 11px;
        color: #94a3b8;
        margin-bottom: 4px;
        margin-left: 4px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* System / Escalation Form */
    .ai-escalation-form {
        background: white;
        border: 1px solid var(--ai-border);
        padding: 16px;
        border-radius: 12px;
        margin-top: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .ai-form-group {
        margin-bottom: 12px;
    }

    .ai-form-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--ai-text);
        margin-bottom: 4px;
    }

    .ai-form-group input, .ai-form-group textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--ai-border);
        border-radius: 8px;
        font-size: 13px;
        outline: none;
        transition: border-color 0.2s;
    }

    .ai-form-group input:focus, .ai-form-group textarea:focus {
        border-color: var(--ai-primary);
    }

    .ai-btn {
        width: 100%;
        padding: 10px;
        background: var(--ai-primary);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: background 0.2s;
    }

    .ai-btn:hover { background: var(--ai-primary-hover); }

    .ai-btn-secondary {
        background: #f1f5f9;
        color: var(--ai-text);
        margin-top: 8px;
    }
    .ai-btn-secondary:hover { background: #e2e8f0; }

    /* Input Area */
    .ai-chat-input-area {
        padding: 16px;
        background: white;
        border-top: 1px solid var(--ai-border);
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .ai-chat-input {
        flex: 1;
        background: #f1f5f9;
        border: 1px solid transparent;
        padding: 12px 16px;
        border-radius: 24px;
        font-size: 14px;
        outline: none;
        transition: all 0.2s;
    }

    .ai-chat-input:focus {
        background: white;
        border-color: var(--ai-primary);
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
    }

    .ai-chat-send {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--ai-primary);
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
        flex-shrink: 0;
    }

    .ai-chat-send:hover { background: var(--ai-primary-hover); }
    .ai-chat-send:disabled { background: #cbd5e1; cursor: not-allowed; }

    /* Typing Indicator */
    .ai-typing {
        display: flex;
        gap: 4px;
        padding: 6px 4px;
    }
    .ai-typing span {
        width: 6px;
        height: 6px;
        background: #cbd5e1;
        border-radius: 50%;
        animation: ai-bounce 1.4s infinite ease-in-out both;
    }
    .ai-typing span:nth-child(1) { animation-delay: -0.32s; }
    .ai-typing span:nth-child(2) { animation-delay: -0.16s; }
    
    @keyframes ai-bounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }

    /* Suggestions */
    .ai-suggestions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }
    .ai-suggestion-chip {
        background: white;
        border: 1px solid var(--ai-primary);
        color: var(--ai-primary);
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .ai-suggestion-chip:hover {
        background: var(--ai-primary);
        color: white;
    }

    @media (max-width: 480px) {
        .ai-chat-window {
            width: calc(100vw - 32px);
            right: 16px;
            bottom: 84px;
            height: calc(100vh - 120px);
        }
    }
</style>

<div id="ai-chat-widget">
    <div class="ai-chat-window" id="aiChatWindow">
        <div class="ai-chat-header">
            <div class="ai-chat-header-info">
                <div class="ai-chat-avatar">{!! $aiAvatarIcon !!}</div>
                <div>
                    <div class="ai-chat-title">{{ $aiTitle }}</div>
                    <div class="ai-chat-status">AI Assistant</div>
                </div>
            </div>
            <button class="ai-chat-close" id="aiChatClose" aria-label="Close Chat">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div id="aiChatIntake" style="display: flex; flex-direction: column; flex: 1; padding: 30px; background: white; text-align: center; justify-content: center; overflow-y: auto;">
            <div style="margin-bottom: 24px;">
                <div style="width: 64px; height: 64px; background: var(--ai-gradient); border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 10px 20px rgba(59,130,246,0.2);">{!! $aiAvatarIcon !!}</div>
                <h3 style="font-size: 18px; font-weight: 800; color: var(--ai-dark); margin: 0 0 8px;">Welcome to Support!</h3>
                <p style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.5;">Please enter your details below so we can assist you better.</p>
            </div>
            
            <div class="ai-form-group" style="text-align: left; margin-bottom: 16px;">
                <label style="display: block; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Your Name</label>
                <input type="text" id="intakeName" placeholder="e.g. Rakib Hasan" value="{{ auth()->check() ? auth()->user()->name : '' }}" style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; outline: none; font-size: 14px; box-sizing: border-box; font-family: inherit;">
            </div>
            <div class="ai-form-group" style="text-align: left; margin-bottom: 24px;">
                <label style="display: block; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Phone / Email</label>
                <input type="text" id="intakeContact" placeholder="e.g. 015XXXXXXXX or email" value="{{ auth()->check() ? auth()->user()->email : '' }}" style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; outline: none; font-size: 14px; box-sizing: border-box; font-family: inherit;">
            </div>
            <button onclick="window.startAiChat()" style="width: 100%; padding: 14px; border-radius: 12px; border: none; background: var(--ai-primary); color: white; font-weight: 700; font-size: 14px; cursor: pointer; box-shadow: 0 10px 20px rgba(59,130,246,0.25); transition: opacity 0.2s; font-family: inherit;">Start Chatting</button>
        </div>

        <div id="aiChatConversation" style="display: none; flex-direction: column; flex: 1; overflow: hidden;">
            <div class="ai-chat-messages" id="aiChatMessages">
                <div class="ai-message ai">
                    <div class="ai-message-label">AI Assistant</div>
                    <div class="ai-message-bubble">
                        {!! nl2br(e($aiWelcome)) !!}
                        
                        <div class="ai-suggestions">
                            <button class="ai-suggestion-chip" onclick="window.sendAiMessage('How does MigratePay work?')">How does MigratePay work?</button>
                            <button class="ai-suggestion-chip" onclick="window.sendAiMessage('How do I create a deal?')">How do I create a deal?</button>
                            <button class="ai-suggestion-chip" onclick="window.sendAiMessage('What happens if the seller does not deliver?')">Seller doesn't deliver?</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="ai-chat-input-area">
                <input type="text" id="aiChatInput" class="ai-chat-input" placeholder="Ask MigratePay Support..." autocomplete="off">
                <button id="aiChatSendBtn" class="ai-chat-send" aria-label="Send Message">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </button>
            </div>
        </div>
    </div>

    <button class="ai-chat-launcher" id="aiChatLauncher" aria-label="Open AI Support">
        <div class="ai-launcher-open">{!! $aiLauncherIcon !!}</div>
        <div class="ai-launcher-close" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </div>
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const launcher = document.getElementById('aiChatLauncher');
        const windowEl = document.getElementById('aiChatWindow');
        const closeBtn = document.getElementById('aiChatClose');
        const messagesArea = document.getElementById('aiChatMessages');
        const inputField = document.getElementById('aiChatInput');
        const sendBtn = document.getElementById('aiChatSendBtn');

        const sessionId = 'ses_' + Math.random().toString(36).substr(2, 9);
        let isWaitingForResponse = false;
        
        // Add CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const launcherOpen = document.querySelector('.ai-launcher-open');
        const launcherClose = document.querySelector('.ai-launcher-close');

        function toggleLauncherIcon(isOpen) {
            if (isOpen) {
                if (launcherOpen) launcherOpen.style.display = 'none';
                if (launcherClose) launcherClose.style.display = 'block';
            } else {
                if (launcherOpen) launcherOpen.style.display = 'block';
                if (launcherClose) launcherClose.style.display = 'none';
            }
        }

        launcher.addEventListener('click', () => {
            if (windowEl.classList.contains('active')) {
                windowEl.classList.remove('active');
                toggleLauncherIcon(false);
            } else {
                windowEl.classList.add('active');
                toggleLauncherIcon(true);
                if(document.getElementById('aiChatConversation').style.display !== 'none') {
                    inputField.focus();
                }
            }
        });

        closeBtn.addEventListener('click', () => {
            windowEl.classList.remove('active');
            toggleLauncherIcon(false);
        });

        window.startAiChat = function() {
            const name = document.getElementById('intakeName').value.trim();
            const contact = document.getElementById('intakeContact').value.trim();
            
            if(!name || !contact) {
                alert('Please provide your name and contact info to start chatting.');
                return;
            }
            
            // Save to session storage so they don't have to re-enter it on reload
            sessionStorage.setItem('ai_chat_name', name);
            sessionStorage.setItem('ai_chat_contact', contact);
            
            document.getElementById('aiChatIntake').style.display = 'none';
            document.getElementById('aiChatConversation').style.display = 'flex';
            inputField.focus();
        }

        // Check if session exists
        if(sessionStorage.getItem('ai_chat_name')) {
            document.getElementById('intakeName').value = sessionStorage.getItem('ai_chat_name');
            document.getElementById('intakeContact').value = sessionStorage.getItem('ai_chat_contact');
            document.getElementById('aiChatIntake').style.display = 'none';
            document.getElementById('aiChatConversation').style.display = 'flex';
        }

        function scrollToBottom() {
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        function appendUserMessage(text) {
            const html = `
                <div class="ai-message user">
                    <div class="ai-message-bubble">${escapeHtml(text)}</div>
                </div>
            `;
            messagesArea.insertAdjacentHTML('beforeend', html);
            scrollToBottom();
        }

        function appendAiMessage(htmlContent) {
            const html = `
                <div class="ai-message ai">
                    <div class="ai-message-label">AI Assistant</div>
                    <div class="ai-message-bubble">${htmlContent}</div>
                </div>
            `;
            messagesArea.insertAdjacentHTML('beforeend', html);
            scrollToBottom();
        }

        function showTyping() {
            const html = `
                <div class="ai-message ai" id="aiTypingIndicator">
                    <div class="ai-message-label">AI Assistant</div>
                    <div class="ai-message-bubble">
                        <div class="ai-typing"><span></span><span></span><span></span></div>
                    </div>
                </div>
            `;
            messagesArea.insertAdjacentHTML('beforeend', html);
            scrollToBottom();
        }

        function hideTyping() {
            const typing = document.getElementById('aiTypingIndicator');
            if (typing) typing.remove();
        }

        function formatAiResponse(text) {
            if (!text) return '';
            
            // Bold
            text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            
            // Italic (simple)
            text = text.replace(/(?<!\w)\*(.*?)\*(?!\w)/g, '<em>$1</em>');
            
            // Lists (English numbers, Bengali numbers, or hyphens/asterisks)
            text = text.replace(/^(\d+[\.\)]|[১২৩৪৫৬৭৮৯০]+[\.\)]|\-|\*) /gm, '<br>&bull; ');
            
            // Links
            text = text.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" target="_blank" style="color: #3b82f6; text-decoration: underline;">$1</a>');
            
            // Newlines
            text = text.replace(/\n/g, '<br>');
            
            // Clean up double <br> that might happen from list parsing
            text = text.replace(/(<br>){3,}/g, '<br><br>');
            
            // Clean up leading <br>
            text = text.replace(/^(<br>)+/, '');
            
            return text;
        }

        window.sendAiMessage = async function(text) {
            if (!text || isWaitingForResponse) return;
            
            appendUserMessage(text);
            inputField.value = '';
            isWaitingForResponse = true;
            sendBtn.disabled = true;

            showTyping();

            try {
                const response = await fetch('/api/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        message: text, 
                        session_id: sessionId,
                        visitor_name: sessionStorage.getItem('ai_chat_name') || '',
                        visitor_contact: sessionStorage.getItem('ai_chat_contact') || ''
                    })
                });

                const data = await response.json();
                hideTyping();

                if (data.escalation_required) {
                    // Show escalation prompt
                    let formattedMsg = formatAiResponse(data.message);
                    let escalationHtml = `
                        <div style="margin-bottom: 12px; line-height: 1.5;">${formattedMsg}</div>
                        <button class="ai-btn ai-btn-secondary mt-2" onclick="window.showEscalationForm('${escapeHtml(text)}')">Talk to a Support Agent</button>
                    `;
                    appendAiMessage(escalationHtml);
                } else {
                    appendAiMessage('<div style="line-height: 1.5;">' + formatAiResponse(data.message) + '</div>');
                }

            } catch (error) {
                hideTyping();
                appendAiMessage("I'm having trouble responding right now. Would you like to contact our support team? <br><br><button class='ai-btn ai-btn-secondary' onclick='window.showEscalationForm(\""+escapeHtml(text)+"\")'>Talk to a Support Agent</button>");
            } finally {
                isWaitingForResponse = false;
                sendBtn.disabled = false;
                inputField.focus();
            }
        }

        window.showEscalationForm = function(originalQuestion) {
            // Remove previous forms if any
            const existingForm = document.getElementById('aiEscalationFormWrapper');
            if(existingForm) existingForm.remove();

            const formHtml = `
                <div class="ai-escalation-form" id="aiEscalationFormWrapper">
                    <p style="font-size: 13px; font-weight: 600; margin-bottom: 12px;">Contact Support Team</p>
                    <div class="ai-form-group">
                        <label>Full Name</label>
                        <input type="text" id="escName" placeholder="John Doe" value="{{ auth()->check() ? auth()->user()->name : '' }}">
                    </div>
                    <div class="ai-form-group">
                        <label>Email Address</label>
                        <input type="email" id="escEmail" placeholder="john@example.com" value="{{ auth()->check() ? auth()->user()->email : '' }}">
                    </div>
                    <div class="ai-form-group">
                        <label>Your Question</label>
                        <textarea id="escQuestion" rows="3">${escapeHtml(originalQuestion)}</textarea>
                    </div>
                    <button class="ai-btn" onclick="window.submitEscalation()">Submit Support Request</button>
                    <button class="ai-btn ai-btn-secondary" onclick="document.getElementById('aiEscalationFormWrapper').remove()">Cancel</button>
                </div>
            `;
            appendAiMessage(formHtml);
        }

        window.submitEscalation = async function() {
            const name = document.getElementById('escName').value;
            const email = document.getElementById('escEmail').value;
            const question = document.getElementById('escQuestion').value;

            if(!name || !email || !question) {
                alert('Please fill out all fields.');
                return;
            }

            const wrapper = document.getElementById('aiEscalationFormWrapper');
            wrapper.innerHTML = `<div class="ai-typing"><span></span><span></span><span></span></div>`;

            try {
                const response = await fetch('/api/ai/escalate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name, email, question })
                });

                const data = await response.json();
                
                if(data.success) {
                    wrapper.innerHTML = `
                        <div style="text-align: center; padding: 10px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 8px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            <p style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">Support Request Sent</p>
                            <p style="font-size: 12px; color: #64748b;">${data.message}</p>
                        </div>
                    `;
                } else {
                    wrapper.innerHTML = `<p style="color: red; font-size: 13px;">Error: ${data.message || 'Something went wrong.'}</p><button class="ai-btn ai-btn-secondary" onclick="window.showEscalationForm('${escapeHtml(question)}')">Try Again</button>`;
                }
            } catch (error) {
                wrapper.innerHTML = `<p style="color: red; font-size: 13px;">Network error. Please try again.</p><button class="ai-btn ai-btn-secondary" onclick="window.showEscalationForm('${escapeHtml(question)}')">Try Again</button>`;
            }
        }

        sendBtn.addEventListener('click', () => {
            window.sendAiMessage(inputField.value.trim());
        });

        inputField.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                window.sendAiMessage(inputField.value.trim());
            }
        });

        function escapeHtml(unsafe) {
            return unsafe
                 .replace(/&/g, "&amp;")
                 .replace(/</g, "&lt;")
                 .replace(/>/g, "&gt;")
                 .replace(/"/g, "&quot;")
                 .replace(/'/g, "&#039;");
        }
    });
</script>
