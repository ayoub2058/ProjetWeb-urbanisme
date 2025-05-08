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

/* Flappy Bird Game Styles */
.game-container {
    max-width: 90% !important;
}

.game-wrapper {
    padding: 0 !important;
    width: 100%;
    overflow: hidden;
    border-radius: 10px;
}

.game-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #73bf2e;
    color: white;
    padding: 10px;
}

.game-header h3 {
    margin: 0;
    font-size: 16px;
}

.score {
    font-weight: bold;
}

#flappy-canvas {
    display: block;
    margin: 0 auto;
    background: #70c5ce;
}

.game-controls {
    display: flex;
    justify-content: center;
    padding: 10px;
    background: #ded895;
}

.game-controls button {
    background: #73bf2e;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: bold;
    margin: 0 5px;
    cursor: pointer;
    transition: all 0.2s;
}

.game-controls button:hover {
    background: #5a9f1f;
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
        } else if (lowerMsg.includes('bored') || lowerMsg.includes('boring') || lowerMsg.includes('entertain')) {
            // Start flappy bird game
            setTimeout(() => {
                startFlappyBirdGame();
            }, 500);
            return "Feeling bored? Let's play a game! I've launched Flappy Bird for you. Tap or press any key to flap and avoid the pipes!";
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
    
    // Flappy Bird Game
    function startFlappyBirdGame() {
        const gameContainer = document.createElement('div');
        gameContainer.id = 'flappy-bird-game';
        gameContainer.className = 'chatbot-message bot game-container';
        gameContainer.innerHTML = `
            <div class="message-content game-wrapper">
                <div class="game-header">
                    <h3>Flappy Bird</h3>
                    <span class="score">Score: <span id="score">0</span></span>
                </div>
                <canvas id="flappy-canvas" width="280" height="400"></canvas>
                <div class="game-controls">
                    <button id="start-game">Start Game</button>
                    <button id="reset-game" style="display:none;">Play Again</button>
                </div>
            </div>
        `;
        
        chatbotMessages.appendChild(gameContainer);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        
        // Initialize the game
        initFlappyBird();
    }
    
    function initFlappyBird() {
        const canvas = document.getElementById('flappy-canvas');
        const ctx = canvas.getContext('2d');
        const startBtn = document.getElementById('start-game');
        const resetBtn = document.getElementById('reset-game');
        const scoreDisplay = document.getElementById('score');
        
        // Game state
        let score = 0;
        let gameRunning = false;
        let gameOver = false;
        let countingDown = false;
        let countdownValue = 3;
        
        // Bird properties
        const bird = {
            x: 50,
            y: canvas.height / 2,
            width: 30,
            height: 24,
            gravity: 0.05,  // Extremely minimal gravity
            velocity: 0,
            jump: -3,       // Very gentle jump
            maxFallSpeed: 1.5, // Very low maximum falling speed
            autoHover: true // Auto-hover functionality
        };
        
        // Pipe properties
        const pipes = [];
        const pipeWidth = 50;
        const pipeGap = 180; // Increased gap between pipes for easier passage
        const pipeSpawnInterval = 120; // Increased spawn interval for fewer pipes
        let frameCount = 0;
        
        // Colors
        const colors = {
            background: '#70c5ce',
            bird: '#ffdd00',
            pipe: '#73bf2e',
            ground: '#ded895',
            text: '#ffffff'
        };
        
        // Draw the bird
        function drawBird() {
            ctx.fillStyle = colors.bird;
            ctx.beginPath();
            ctx.arc(bird.x, bird.y, bird.width / 2, 0, Math.PI * 2);
            ctx.fill();
            
            // Add eye to bird
            ctx.fillStyle = 'black';
            ctx.beginPath();
            ctx.arc(bird.x + 8, bird.y - 5, 4, 0, Math.PI * 2);
            ctx.fill();
        }
        
        // Create a pipe
        function createPipe() {
            // Ensure the gap is positioned more in the middle for easier gameplay
            const minTopHeight = 100;  // Increased minimum top pipe height
            const maxTopHeight = canvas.height - pipeGap - 100; // Increased maximum top pipe height
            const topHeight = Math.floor(Math.random() * (maxTopHeight - minTopHeight)) + minTopHeight;
            
            pipes.push({
                x: canvas.width,
                y: 0,
                width: pipeWidth,
                height: topHeight,
                passed: false
            });
            
            pipes.push({
                x: canvas.width,
                y: topHeight + pipeGap,
                width: pipeWidth,
                height: canvas.height - topHeight - pipeGap,
                passed: false
            });
        }
        
        // Draw pipes
        function drawPipes() {
            for (let i = 0; i < pipes.length; i++) {
                const pipe = pipes[i];
                ctx.fillStyle = colors.pipe;
                ctx.fillRect(pipe.x, pipe.y, pipe.width, pipe.height);
            }
        }
        
        // Move pipes
        function movePipes() {
            for (let i = 0; i < pipes.length; i++) {
                const pipe = pipes[i];
                pipe.x -= 0.8; // Extremely slow pipe movement (was 1.2)
                
                // Check if pipe has passed the bird
                if (pipe.x + pipe.width < bird.x && !pipe.passed && i % 2 === 0) {
                    pipe.passed = true;
                    score++;
                    scoreDisplay.textContent = score;
                }
                
                // Remove pipes that have gone off screen
                if (pipe.x + pipe.width <= 0) {
                    pipes.splice(i, 1);
                    i--;
                }
            }
            
            // Create new pipes less frequently
            if (frameCount % (pipeSpawnInterval * 1.5) === 0) {
                createPipe();
            }
        }
        
        // Check for collisions
        function checkCollision() {
            // Add a 3px forgiveness to make collision detection more forgiving
            const forgiveness = 3;
            
            // Ground collision
            if (bird.y + bird.height / 2 - forgiveness >= canvas.height - 20) { // Account for ground height
                return true;
            }
            
            // Ceiling collision
            if (bird.y - bird.height / 2 + forgiveness <= 0) {
                return true;
            }
            
            // Pipe collision with forgiveness
            for (let i = 0; i < pipes.length; i++) {
                const pipe = pipes[i];
                
                if (
                    bird.x + bird.width / 2 - forgiveness > pipe.x &&
                    bird.x - bird.width / 2 + forgiveness < pipe.x + pipe.width &&
                    bird.y + bird.height / 2 - forgiveness > pipe.y &&
                    bird.y - bird.height / 2 + forgiveness < pipe.y + pipe.height
                ) {
                    return true;
                }
            }
            
            return false;
        }
        
        // Draw ground
        function drawGround() {
            ctx.fillStyle = colors.ground;
            ctx.fillRect(0, canvas.height - 20, canvas.width, 20);
        }
        
        // Update game state
        function update() {
            // Clear canvas
            ctx.fillStyle = colors.background;
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Apply gravity to bird only if game is running and not counting down
            if (gameRunning && !countingDown) {
                // Apply gravity
                bird.velocity += bird.gravity;
                
                // Apply terminal velocity - cap the maximum falling speed
                if (bird.velocity > bird.maxFallSpeed) {
                    bird.velocity = bird.maxFallSpeed;
                }
                
                // Apply auto-hover effect - if the bird starts falling too fast, add some upward movement
                if (bird.autoHover && bird.velocity > 1) {
                    // Randomly apply small upward forces to simulate flapping
                    if (Math.random() < 0.1) {
                        bird.velocity -= 0.8;
                    }
                }
                
                // Apply very small hover oscillation
                bird.y += Math.sin(frameCount * 0.1) * 0.3;
                
                // Move bird based on velocity
                bird.y += bird.velocity;
            } else {
                // Add a slight oscillation to the bird when counting down
                bird.y += Math.sin(frameCount * 0.1) * 0.5;
            }
            
            // Draw bird
            drawBird();
            
            // Move and draw pipes
            movePipes();
            drawPipes();
            
            // Draw ground
            drawGround();
            
            // Check for collision
            if (checkCollision()) {
                gameOver = true;
                gameRunning = false;
                resetBtn.style.display = 'block';
                
                // Draw game over text
                ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = colors.text;
                ctx.font = '24px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('Game Over', canvas.width / 2, canvas.height / 2);
                ctx.font = '18px Arial';
                ctx.fillText(`Score: ${score}`, canvas.width / 2, canvas.height / 2 + 30);
                return;
            }
            
            // Increment frame count
            frameCount++;
            
            // Continue game loop
            if (gameRunning) {
                requestAnimationFrame(update);
            }
        }
        
        // Jump function
        function jump() {
            if ((gameRunning && !gameOver && !countingDown) || countingDown) {
                bird.velocity = bird.jump;
            }
        }
        
        // Event listeners
        startBtn.addEventListener('click', function() {
            if (!gameRunning && !gameOver && !countingDown) {
                // Start countdown instead of game
                countingDown = true;
                startBtn.style.display = 'none';
                startCountdown();
            }
        });
        
        resetBtn.addEventListener('click', function() {
            // Reset game state
            bird.y = canvas.height / 2;
            bird.velocity = 0;
            pipes.length = 0;
            score = 0;
            scoreDisplay.textContent = score;
            frameCount = 0;
            gameOver = false;
            
            // Hide reset button
            resetBtn.style.display = 'none';
            
            // Start game
            gameRunning = true;
            update();
        });
        
        // Countdown function
        function startCountdown() {
            // Show countdown on canvas
            ctx.fillStyle = colors.background;
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            drawBird();
            drawGround();
            
            // Draw countdown number
            ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = colors.text;
            ctx.font = '48px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(countdownValue.toString(), canvas.width / 2, canvas.height / 2);
            
            // Decrement countdown and continue or start game
            countdownValue--;
            
            if (countdownValue >= 0) {
                // Continue countdown
                setTimeout(startCountdown, 1000);
            } else {
                // Countdown finished, start game
                countingDown = false;
                countdownValue = 3; // Reset for next time
                gameRunning = true;
                
                // Give the bird an initial upward movement when game starts
                bird.velocity = bird.jump * 0.6;
                
                update();
            }
        }
        
        // Add event listeners for controls
        window.addEventListener('keydown', function(e) {
            if (e.key === ' ' || e.key === 'ArrowUp') {
                jump();
            }
        });
        
        canvas.addEventListener('click', jump);
        canvas.addEventListener('touchstart', function(e) {
            e.preventDefault();
            jump();
        });
        
        // Draw initial state
        ctx.fillStyle = colors.background;
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        drawBird();
        drawGround();
        
        // Initial instructions
        ctx.fillStyle = colors.text;
        ctx.font = '16px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('Click Start to play!', canvas.width / 2, canvas.height / 2);
        ctx.fillText('Tap or press Space to flap', canvas.width / 2, canvas.height / 2 + 30);
    }
});
</script>
</body>
</html> 