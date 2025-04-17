// Initialize the page on document load
document.addEventListener('DOMContentLoaded', function() {
    // Apply dark mode styles to the page
    applyDarkMode();
    
    // Create and add the Spline background
    createSplineBackground();
});

function applyDarkMode() {
    // Create a style element
    const styleEl = document.createElement('style');
    styleEl.textContent = `
        body {
            background-color: #121212 !important;
            color: #e0e0e0 !important;
        }
        
        .contact-main {
            background-color: transparent !important;
            position: relative;
            z-index: 2;
        }
        
        .contact-hero {
            background: linear-gradient(135deg, rgba(26, 26, 46, 0.8) 0%, rgba(22, 33, 62, 0.8) 100%) !important;
            padding: 60px 20px !important;
            text-align: center !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            margin-bottom: 40px !important;
            backdrop-filter: blur(5px) !important;
        }
        
        .contact-hero h1 {
            margin-bottom: 20px !important;
            font-size: 2.5rem !important;
            color: #00ffff !important;
            text-shadow: 0 0 10px rgba(0, 255, 255, 0.5) !important;
        }
        
        .contact-hero p {
            font-size: 1.2rem !important;
            max-width: 700px !important;
            margin: 0 auto !important;
            color: #b3b3b3 !important;
        }
        
        .contact-form-container, .contact-info-container {
            background-color: rgba(30, 30, 30, 0.8) !important;
            border-radius: 8px !important;
            box-shadow: 0 8px 30px rgba(0, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px) !important;
        }
        
        .contact-form-container h2, .contact-info-container h2,
        .ticket-history-section h2 {
            color: #00ffff !important;
            border-bottom: 2px solid #00ffff !important;
        }
        
        .contact-form label, .filter-group label {
            color: #b3b3b3 !important;
        }
        
        .contact-form input, .contact-form textarea,
        .filter-select, .ticket-search input, .ticket-search button {
            background-color: rgba(45, 45, 45, 0.8) !important;
            border: 1px solid #333 !important;
            color: #e0e0e0 !important;
        }
        
        .contact-form input:focus, .contact-form textarea:focus {
            border-color: #00ffff !important;
            box-shadow: 0 0 0 2px rgba(0, 255, 255, 0.3) !important;
        }
        
        .contact-form button, .submit-btn {
            background: linear-gradient(135deg, #00ffff, #0c9db5) !important;
            color: #121212 !important;
        }
        
        .contact-form button:hover, .submit-btn:hover {
            background: linear-gradient(135deg, #0c9db5, #00ffff) !important;
            transform: translateY(-3px) !important;
            box-shadow: 0 10px 20px rgba(0, 255, 255, 0.3) !important;
        }
        
        .info-item i {
            background-color: rgba(0, 255, 255, 0.1) !important;
            color: #00ffff !important;
        }
        
        .info-item p {
            color: #b3b3b3 !important;
        }
        
        .social-links a {
            background-color: rgba(0, 255, 255, 0.1) !important;
            color: #00ffff !important;
        }
        
        .social-links a:hover {
            background-color: #00ffff !important;
            color: #121212 !important;
        }
        
        .alert-success {
            background-color: rgba(0, 255, 128, 0.2) !important;
            color: #2ecc71 !important;
            border: 1px solid rgba(46, 204, 113, 0.5) !important;
        }
        
        .alert-danger {
            background-color: rgba(255, 0, 0, 0.2) !important;
            color: #ff6b6b !important;
            border: 1px solid rgba(255, 107, 107, 0.5) !important;
        }
        
        .ticket-history-section {
            background-color: rgba(30, 30, 30, 0.8) !important;
            padding: 20px !important;
            border-radius: 8px !important;
            box-shadow: 0 8px 30px rgba(0, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px) !important;
            position: relative;
            z-index: 2;
        }
        
        .view-btn {
            background-color: #2d2d2d !important;
            border: 1px solid #333 !important;
            color: #b3b3b3 !important;
        }
        
        .view-btn.active, .view-btn:hover {
            background-color: rgba(0, 255, 255, 0.1) !important;
            color: #00ffff !important;
        }
        
        footer {
            background-color: rgba(18, 18, 18, 0.8) !important;
            color: #e0e0e0 !important;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(5px) !important;
        }
        
        #spline-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }
    `;
    
    // Add the style element to the head
    document.head.appendChild(styleEl);
}

function createSplineBackground() {
    // Create a container for the Spline viewer
    let splineContainer = document.getElementById('canvas-container');
    
    // If canvas container doesn't exist, create a new one
    if (!splineContainer) {
        splineContainer = document.createElement('div');
        splineContainer.id = 'spline-container';
        document.body.prepend(splineContainer); // Add it to the beginning of the body
    } else {
        // Clear existing container
        splineContainer.innerHTML = '';
        splineContainer.id = 'spline-container';
    }
    
    // Create the spline-viewer element
    const splineViewer = document.createElement('spline-viewer');
    
    // Set dark-themed Spline URL - this is a space/abstract themed scene
    // This is a dark cosmic themed scene with particles/nebula effects
    splineViewer.setAttribute('url', 'https://prod.spline.design/PsJxEnlUjW0RRq-i/scene.splinecode');
    
    // Add Spline-specific attributes
    splineViewer.setAttribute('loading-anim-type', 'spinner');
    splineViewer.setAttribute('auto-rotate', 'true');
    splineViewer.setAttribute('ambient-intensity', '0.7');
    
    // Set some custom attributes via style
    splineViewer.style.width = '100%';
    splineViewer.style.height = '100%';
    splineViewer.style.opacity = '0.8';
    
    // Add the spline viewer to the container
    splineContainer.appendChild(splineViewer);
    
    // Make sure the Spline script is loaded
    if (!document.querySelector('script[src*="splinetool"]')) {
        const script = document.createElement('script');
        script.type = 'module';
        script.src = 'https://unpkg.com/@splinetool/viewer@1.9.82/build/spline-viewer.js';
        document.head.appendChild(script);
    }
} 