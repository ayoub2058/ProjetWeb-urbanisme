<?php
require_once '../../controllers/ContactController.php';

$controller = new ContactController();

// Handle delete action
if (isset($_GET['delete_id'])) {
    $controller->deleteMessage($_GET['delete_id']);
    echo "<script>alert('Ticket deleted successfully!'); window.location.href = 'ticket.php';</script>";
}

// Handle edit action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $data = [
        'name' => $_POST['edit-name'],
        'email' => $_POST['edit-email'],
        'subject' => $_POST['edit-subject'],
        'message' => $_POST['edit-message'],
    ];
    $controller->updateMessage($_POST['edit_id'], $data);
    echo "<script>alert('Ticket updated successfully!'); window.location.href = 'ticket.php';</script>";
}

// Fetch all tickets
$tickets = $controller->getAllMessages();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Tickets - Clyptor</title>
</head>

<body>
    <div class="container">
        <!-- Sidebar Section -->
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
                <a href="admin.php">
                    <span class="material-icons-sharp">
                        dashboard
                    </span>
                    <h3>Dashboard</h3>
                </a>
                <a href="ticket.php" class="active">
                    <span class="material-icons-sharp">
                        mail_outline
                    </span>
                    <h3>Tickets</h3>
                </a>
                <a href="#">
                    <span class="material-icons-sharp">
                        logout
                    </span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>
        <!-- End of Sidebar Section -->

        <!-- Main Content -->
        <main>
            <h1>Tickets</h1>
            <div class="tickets">
                <table>
                    <thead>
                        <tr>
                            <th>Sender Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><?= htmlspecialchars($ticket['name']) ?></td>
                                <td><?= htmlspecialchars($ticket['email']) ?></td>
                                <td><?= htmlspecialchars($ticket['subject']) ?></td>
                                <td><?= nl2br(htmlspecialchars($ticket['message'])) ?></td>
                                <td><?= $ticket['created_at'] ?></td>
                                <td>
                                    <a href="?delete_id=<?= $ticket['message_id'] ?>" onclick="return confirm('Are you sure you want to delete this ticket?');" class="btn btn-danger">Delete</a>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="edit_id" value="<?= $ticket['message_id'] ?>">
                                        <input type="text" name="edit-name" value="<?= htmlspecialchars($ticket['name']) ?>" required>
                                        <input type="email" name="edit-email" value="<?= htmlspecialchars($ticket['email']) ?>" required>
                                        <input type="text" name="edit-subject" value="<?= htmlspecialchars($ticket['subject']) ?>" required>
                                        <textarea name="edit-message" required><?= htmlspecialchars($ticket['message']) ?></textarea>
                                        <button type="submit" class="btn btn-primary">Edit</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
        <!-- End of Main Content -->
    </div>
</body>

</html>