// Versión local del chatbot de burbuja
// No usa PHP y muestra chatbot.html en un iframe

class BubbleChatbotLocal {
    constructor() {
        this.isOpen = false;
        this.init();
    }

    init() {
        if (document.body) {
            this.createBubbleButton();
            this.createChatbotFrameContainer();
        } else {
            document.addEventListener('DOMContentLoaded', () => {
                this.createBubbleButton();
                this.createChatbotFrameContainer();
            });
        }
    }

    createBubbleButton() {
        if (document.getElementById('bubble-chatbot-button')) return;

        const bubbleButton = document.createElement('div');
        bubbleButton.id = 'bubble-chatbot-button';
        bubbleButton.innerHTML = `
            <div class="bubble-icon">
                <i class="fas fa-robot"></i>
            </div>
            <div class="bubble-pulse"></div>
        `;
        bubbleButton.addEventListener('click', () => this.toggleChatbot());

        document.body.appendChild(bubbleButton);
    }

    createChatbotFrameContainer() {
        if (document.getElementById('bubble-chatbot-frame-container')) return;

        const container = document.createElement('div');
        container.id = 'bubble-chatbot-frame-container';
        container.innerHTML = `
            <div class="chatbot-header">
                <div class="chatbot-title">
                    <i class="fas fa-robot"></i>
                    <span>Aicroombot</span>
                </div>
                <button class="chatbot-minimize" id="chatbotFrameCloseBtn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="chatbot-frame-content">
                <iframe src="chatbot.html" frameborder="0" id="chatbotFrame"></iframe>
            </div>
        `;

        document.body.appendChild(container);

        const closeBtn = document.getElementById('chatbotFrameCloseBtn');
        closeBtn.addEventListener('click', () => this.toggleChatbot());

        this.addStyles();

        // Inicialmente oculto
        container.style.display = 'none';
    }

    toggleChatbot() {
        const container = document.getElementById('bubble-chatbot-frame-container');
        const button = document.getElementById('bubble-chatbot-button');

        if (!container || !button) return;

        if (!this.isOpen) {
            container.style.display = 'flex';
            button.style.display = 'none';
            this.isOpen = true;
        } else {
            container.style.display = 'none';
            button.style.display = 'flex';
            this.isOpen = false;
        }
    }

    addStyles() {
        const styleId = 'bubble-chatbot-local-styles';
        if (document.getElementById(styleId)) return;

        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = `
            #bubble-chatbot-button {
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 1000;
            }

            #bubble-chatbot-button .bubble-icon {
                color: white;
                font-size: 24px;
            }

            #bubble-chatbot-frame-container {
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 450px;
                height: 650px;
                background: white;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.15);
                z-index: 1001;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

            #bubble-chatbot-frame-container .chatbot-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 12px 16px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            #bubble-chatbot-frame-container .chatbot-title {
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 600;
            }

            #bubble-chatbot-frame-container .chatbot-minimize {
                background: transparent;
                border: none;
                color: white;
                cursor: pointer;
                font-size: 16px;
            }

            .chatbot-frame-content {
                flex: 1;
                background: #f5f7fa;
            }

            .chatbot-frame-content iframe {
                width: 100%;
                height: 100%;
            }

            @media (max-width: 600px) {
                #bubble-chatbot-frame-container {
                    width: 95vw;
                    height: 80vh;
                    right: 2.5vw;
                    bottom: 10px;
                }
            }
        `;

        document.head.appendChild(style);
    }
}

// Instanciar automáticamente en páginas que carguen este script
window.addEventListener('load', () => {
    window.bubbleChatbotLocal = new BubbleChatbotLocal();
});
