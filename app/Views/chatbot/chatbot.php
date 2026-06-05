    <link rel="stylesheet" href="<?= base_url('assets/css/chatbot.css') ?>">

    <!-- Bouton flottant -->
    <button id="chatbot-toggle" title="Assistant IA">
        <i class="fas fa-robot"></i>
    </button>

    <!-- Fenêtre chatbot -->
    <div id="chatbot-container">
        <div id="chatbot-header">
            <span><i class="fas fa-robot me-2"></i>Assistant IA</span>
            <button id="chatbot-close"><i class="fas fa-times"></i></button>
        </div>
        <div id="chatbot-body"></div>
        <div id="chatbot-footer">
            <input type="text" id="chatbot-input" placeholder="Posez votre question...">
            <button id="chatbot-send"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script type="text/javascript" src="<?= base_url('assets/js/pages/chatbot.js'); ?>"></script>