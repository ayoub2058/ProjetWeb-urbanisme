<?php
// No PHP processing needed for this file currently
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Functions</title>
</head>
<body>
    <!-- AI Chatbot Widget -->
    <div class="chatbot-widget" id="chatbot-widget">
        <div class="chatbot-header">
            <h3>Clyptor Assistant</h3>
            <button id="chatbot-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="chatbot-messages" id="chatbot-messages">
            <div class="chatbot-message bot">
                <div class="message-content">
                    Hello! I'm Clyptor's AI assistant. How can I help you today?
                </div>
            </div>
        </div>
        <div class="chatbot-input">
            <input type="text" id="chatbot-user-input" placeholder="Type your message...">
            <button id="chatbot-send"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
    <button class="chatbot-toggle" id="chatbot-toggle">
        <i class="fas fa-robot"></i>
    </button>

<style>
/* Chatbot styling */
.chatbot-toggle {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3498db, #2ecc71);
    color: white;
    border: none;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    z-index: 999;
    transition: all 0.3s ease;
}

.chatbot-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}

.chatbot-widget {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    display: none;
    flex-direction: column;
    overflow: hidden;
}

@media (max-width: 992px) {
    .chatbot-widget {
        width: calc(100% - 60px);
        right: 30px;
        left: 30px;
    }
}

@media (max-width: 480px) {
    .chatbot-widget {
        bottom: 80px;
        height: calc(100vh - 160px);
        width: calc(100% - 40px);
        right: 20px;
        left: 20px;
    }
    
    .chatbot-toggle {
        width: 50px;
        height: 50px;
        bottom: 20px;
        right: 20px;
        font-size: 1.2rem;
    }
}

/* Add chatbot header, messages, and input styles */
.chatbot-header {
    background: linear-gradient(135deg, #3498db, #2ecc71);
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-header h3 {
    margin: 0;
    font-size: 1.2rem;
}

.chatbot-header button {
    background: none;
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
}

.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    display: flex;
    flex-direction: column;
}

.chatbot-message {
    margin-bottom: 15px;
    max-width: 80%;
}

.chatbot-message.bot {
    align-self: flex-start;
}

.chatbot-message.user {
    align-self: flex-end;
}

.message-content {
    padding: 10px 15px;
    border-radius: 18px;
    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
}

.chatbot-message.bot .message-content {
    background-color: #f1f1f1;
    color: #333;
}

.chatbot-message.user .message-content {
    background: linear-gradient(135deg, #3498db, #2ecc71);
    color: white;
}

.chatbot-input {
    display: flex;
    padding: 10px;
    border-top: 1px solid #eee;
}

.chatbot-input input {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 30px;
    outline: none;
}

.chatbot-input button {
    background: linear-gradient(135deg, #3498db, #2ecc71);
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-left: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize chatbot
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const chatbotWidget = document.getElementById('chatbot-widget');
    const chatbotClose = document.getElementById('chatbot-close');
    const chatbotSend = document.getElementById('chatbot-send');
    const chatbotInput = document.getElementById('chatbot-user-input');
    const chatbotMessages = document.getElementById('chatbot-messages');
    
    let chatbotOpen = false;
    
    chatbotToggle.addEventListener('click', function() {
        chatbotOpen = !chatbotOpen;
        if (chatbotOpen) {
            chatbotWidget.style.display = 'flex';
            chatbotToggle.innerHTML = '<i class="fas fa-times"></i>';
        } else {
            chatbotWidget.style.display = 'none';
            chatbotToggle.innerHTML = '<i class="fas fa-robot"></i>';
        }
    });
    
    chatbotClose.addEventListener('click', function() {
        chatbotOpen = false;
        chatbotWidget.style.display = 'none';
        chatbotToggle.innerHTML = '<i class="fas fa-robot"></i>';
    });
    
    function addBotMessage(text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chatbot-message bot';
        messageDiv.innerHTML = `<div class="message-content">${text}</div>`;
        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }
    
    function addUserMessage(text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chatbot-message user';
        messageDiv.innerHTML = `<div class="message-content">${text}</div>`;
        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }
    
    function sendMessage() {
        const message = chatbotInput.value.trim();
        if (message === '') return;
        
        addUserMessage(message);
        chatbotInput.value = '';
        
        // Simulate AI response (in a real app, this would call an API)
        setTimeout(() => {
            const response = generateAIResponse(message);
            addBotMessage(response);
        }, 1000);
    }
    
    chatbotSend.addEventListener('click', sendMessage);
    
    chatbotInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    
    // Simple AI response generator
    function generateAIResponse(message) {
        const lowerMsg = message.toLowerCase();
        
        if (lowerMsg.includes('hello') || lowerMsg.includes('hi')) {
            return "Hello there! How can I assist you with Clyptor services today?";
        } else if (lowerMsg.includes('carpool') || lowerMsg.includes('covoiturage')) {
            return "Our carpooling service connects drivers with passengers heading the same way. You can save money on your commute or long trips by sharing rides. Would you like help finding a ride or listing your vehicle?";
        } else if (lowerMsg.includes('home') || lowerMsg.includes('rent') || lowerMsg.includes('property')) {
            return "Our home rental platform allows you to list or find vacation rentals, apartments, and rooms. Are you looking to rent out your property or find a place to stay?";
        } else if (lowerMsg.includes('car') || lowerMsg.includes('vehicle')) {
            return "The car rental service lets you rent vehicles from local owners by the hour, day, or week. You can also list your own car to earn money when you're not using it. How can I help with car rentals?";
        } else if (lowerMsg.includes('account') || lowerMsg.includes('login') || lowerMsg.includes('register')) {
            return "You can create an account or login from any page using the top navigation. Having an account allows you to create listings, contact other users, and manage your posts. Would you like me to direct you to the registration page?";
        } else if (lowerMsg.includes('help') || lowerMsg.includes('support')) {
            return "I can help answer questions about our services. For specific account issues or complaints, please use the contact form on this page to reach our human support team.";
        } else if (lowerMsg.includes('thank') || lowerMsg.includes('thanks')) {
            return "You're welcome! Is there anything else I can help you with?";
        } else if (lowerMsg.includes('bye') || lowerMsg.includes('goodbye')) {
            return "Goodbye! Don't hesitate to reach out if you have more questions.";
        } else {
            const randomResponses = [
                "I'm here to help with Clyptor services. Could you tell me more about what you need?",
                "That's an interesting question. I can provide information about our carpooling, home rental, and car rental services. Which one are you interested in?",
                "I'm still learning! For complex questions, please use the contact form to reach our support team.",
                "Let me help you with that. Are you asking about our sharing services?"
            ];
            return randomResponses[Math.floor(Math.random() * randomResponses.length)];
        }
    }
});
</script>
</body>
</html> 