
const chatBody = document.getElementById('chatbot-body');
const chatInput = document.getElementById('chatbot-input');
const chatSend = document.getElementById('chatbot-send');
const chatToggle = document.getElementById('chatbot-toggle');
const chatClose = document.getElementById('chatbot-close');
const chatContainer = document.getElementById('chatbot-container');

let history = [];
let isOpen = false;

// Ouvrir / fermer
chatToggle.addEventListener('click', () => {
    isOpen = !isOpen;
    chatContainer.classList.toggle('open', isOpen);
    if (isOpen && chatBody.children.length === 0) {
        appendMessage('bot', 'Bonjour ! Je suis votre assistant de gestion. Que puis-je faire pour vous ?');
    }
    if (isOpen) chatInput.focus();
});
chatClose.addEventListener('click', () => {
    isOpen = false;
    chatContainer.classList.remove('open');
});

// Convertir le markdown basique en HTML
function parseMarkdown(text) {
    return text
        // Gras **texte**
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        // Italique *texte*
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        // Titre ## texte
        .replace(/^## (.*$)/gim, '<h6 class="mb-1 mt-2">$1</h6>')
        // Titre # texte
        .replace(/^# (.*$)/gim, '<h5 class="mb-1 mt-2">$1</h5>')
        // Liste - item
        .replace(/^\- (.*$)/gim, '<li>$1</li>')
        // Entourer les <li> dans un <ul>
        .replace(/(<li>.*<\/li>)/gs, '<ul class="mb-1 ps-3">$1</ul>')
        // Saut de ligne
        .replace(/\n/g, '<br>');
}

// Ajouter un message
function appendMessage(role, text) {
    const isUser = role === 'user';
    const div = document.createElement('div');
    div.className = `cb-msg ${isUser ? 'user' : 'bot'}`;
    div.innerHTML = `<div class="cb-bubble">${isUser ? text : parseMarkdown(text)}</div>`;
    chatBody.appendChild(div);
    chatBody.scrollTop = chatBody.scrollHeight;
}

// Indicateur frappe
function showTyping() {
    const div = document.createElement('div');
    div.id = 'cb-typing';
    div.className = 'cb-msg bot';
    div.innerHTML = `<div class="cb-bubble d-flex align-items-center gap-1" style="padding:10px 14px">
        <span class="typing-dot"></span>
        <span class="typing-dot"></span>
        <span class="typing-dot"></span>
    </div>`;
    chatBody.appendChild(div);
    chatBody.scrollTop = chatBody.scrollHeight;
}

function hideTyping() {
    document.getElementById('cb-typing')?.remove();
}

// Envoyer un message
async function sendMessage() {
    const message = chatInput.value.trim();
    if (!message) return;

    appendMessage('user', message);
    history.push({
        role: 'user',
        content: message
    });
    chatInput.value = '';
    chatSend.disabled = true;
    showTyping();

    try {
        const res = await fetch('/ChatbotController/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                message,
                history
            }),
        });
        const data = await res.json();
        hideTyping();

        const reply = data.reply || data.error || 'Erreur inconnue.';
        appendMessage('bot', reply);
        history.push({
            role: 'assistant',
            content: reply
        });
    } catch (e) {
        hideTyping();
        appendMessage('bot', 'Erreur de connexion.');
    } finally {
        chatSend.disabled = false;
        chatInput.focus();
    }
}

chatSend.addEventListener('click', sendMessage);
chatInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') sendMessage();
});