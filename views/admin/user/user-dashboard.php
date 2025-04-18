<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside>
      <div class="toggle">
        <div class="logo">
            <img src="images/logo.png">
            <h2>Cly<span class="danger">Ptor</span></h2>
        </div>
        <div class="close" id="close-btn">
            <span class="material-icons-sharp">
                close
            </span>
        </div>
    </div>
      <div class="sidebar">
        <a href="user-info.php" class="active">
          <span class="fas fa-user"></span>
          <h3>User Information</h3>
        </a>
        <a href="history.php">
          <span class="fas fa-history"></span>
          <h3>History</h3>
        </a>
        <a href="chat.php">
          <span class="fas fa-comments"></span>
          <h3>Chat</h3>
        </a>
        <a href="#" id="logout-button">
          <span class="fas fa-sign-out-alt"></span>
          <h3>Logout</h3>
        </a>
      </div>
    </aside>

    <!-- Main Content -->
    <main>
      <h1>Welcome to Your Dashboard</h1>

      <!-- User Information Section -->
      <section id="user-info" class="analyse">
        <div>
          <h2>User Information</h2>
          <p>View and update your personal details.</p>
          <div class="card">
            <h3>Username: <span id="username-display">John Doe</span></h3>
            <h3>Email: <span id="email-display">johndoe@example.com</span></h3>
            <button class="btn btn-primary">Edit Information</button>
          </div>
        </div>
      </section>

      <!-- History Section -->
      <section id="history" class="analyse">
        <div>
          <h2>History</h2>
          <p>Check your activity and transaction history.</p>
          <table class="styled-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Activity</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>2025-04-10</td>
                <td>Carpooling</td>
                <td><span class="status published">Completed</span></td>
              </tr>
              <tr>
                <td>2025-04-08</td>
                <td>Home Rent</td>
                <td><span class="status draft">Pending</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Chat Section -->
      <section id="chat" class="analyse">
        <div>
          <h2>Chat</h2>
          <p>Connect and chat with other users.</p>
          <div class="chat-box">
            <div class="messages">
              <div class="message received">
                <p>Hi! How can I help you?</p>
              </div>
              <div class="message sent">
                <p>I'm looking for a carpool to downtown.</p>
              </div>
            </div>
            <form id="chat-form">
              <input type="text" placeholder="Type a message..." required />
              <button type="submit" class="btn btn-primary">Send</button>
            </form>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    // Logout functionality
    document.getElementById("logout-button").addEventListener("click", () => {
      localStorage.removeItem("isLoggedIn");
      localStorage.removeItem("username");
      window.location.href = "../index.html";
    });

    // Chat functionality (basic example)
    const chatForm = document.getElementById("chat-form");
    const messages = document.querySelector(".messages");

    chatForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const input = chatForm.querySelector("input");
      const message = input.value.trim();

      if (message) {
        const sentMessage = document.createElement("div");
        sentMessage.classList.add("message", "sent");
        sentMessage.innerHTML = `<p>${message}</p>`;
        messages.appendChild(sentMessage);
        input.value = "";
        messages.scrollTop = messages.scrollHeight;
      }
    });
  </script>
</body>
</html>