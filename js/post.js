// Create a new post
function createPost(e, serviceType) {
    e.preventDefault();
    
    const user = JSON.parse(localStorage.getItem('current_user'));
    if (!user) {
        alert('Please login to create a post!');
        window.location.href = 'login.html';
        return;
    }
    
    const title = document.getElementById('post-title').value;
    const description = document.getElementById('post-description').value;
    const category = document.getElementById('post-category').value;
    const imageInput = document.getElementById('post-image');
    
    // Simple image handling (in real app, you'd upload to server)
    let imageUrl = '';
    if (imageInput.files && imageInput.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imageUrl = e.target.result;
            savePost(title, description, imageUrl, category, serviceType, user);
        };
        reader.readAsDataURL(imageInput.files[0]);
    } else {
        savePost(title, description, imageUrl, category, serviceType, user);
    }
}

function savePost(title, description, imageUrl, category, serviceType, user) {
    const posts = JSON.parse(localStorage.getItem('clyptor_posts')) || [];
    
    const newPost = {
        id: Date.now(),
        title,
        description,
        image: imageUrl,
        category,
        serviceType,
        userId: user.id,
        userName: user.name,
        userEmail: user.email,
        date: new Date().toISOString(),
        status: 'active'
    };
    
    posts.push(newPost);
    localStorage.setItem('clyptor_posts', JSON.stringify(posts));
    
    alert('Post created successfully!');
    window.location.href = `${serviceType}.html`;
}

// Load posts for a specific service
function loadPosts(serviceType) {
    const posts = JSON.parse(localStorage.getItem('clyptor_posts')) || [];
    const filteredPosts = posts.filter(post => post.serviceType === serviceType && post.status === 'active');
    
    const postsContainer = document.getElementById('posts-container');
    if (postsContainer) {
        postsContainer.innerHTML = '';
        
        if (filteredPosts.length === 0) {
            postsContainer.innerHTML = '<p class="no-posts">No posts available. Be the first to create one!</p>';
            return;
        }
        
        filteredPosts.forEach(post => {
            const postElement = document.createElement('div');
            postElement.className = 'post-card';
            postElement.innerHTML = `
                <div class="post-header">
                    <h3>${post.title}</h3>
                    <span class="post-category">${post.category}</span>
                </div>
                ${post.image ? `<div class="post-image"><img src="${post.image}" alt="${post.title}"></div>` : ''}
                <div class="post-content">
                    <p>${post.description}</p>
                </div>
                <div class="post-footer">
                    <span class="post-author">Posted by ${post.userName}</span>
                    <span class="post-date">${new Date(post.date).toLocaleDateString()}</span>
                </div>
            `;
            postsContainer.appendChild(postElement);
        });
    }
}

// Initialize posts when page loads
document.addEventListener('DOMContentLoaded', function() {
    const postForm = document.getElementById('post-form');
    if (postForm) {
        const serviceType = window.location.pathname.split('/').pop().replace('.html', '');
        postForm.addEventListener('submit', (e) => createPost(e, serviceType));
    }
    
    // Load posts if on a service page
    const path = window.location.pathname;
    if (path.includes('covoiturage.html') || path.includes('home-rent.html') || path.includes('car-rent.html')) {
        const serviceType = path.split('/').pop().replace('.html', '');
        loadPosts(serviceType);
    }
});