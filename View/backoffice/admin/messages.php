<?php
// Vérifier si l'utilisateur est connecté en tant qu'admin
session_start();
if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../../../index.php?page=user&action=login");
    exit();
}

// Inclure les fichiers nécessaires
require_once '../../../config.php';
require_once MODEL_PATH . '/Database.php';
require_once MODEL_PATH . '/Message.php';

// Initialiser la base de données et le modèle Message
$database = new Database();
$db = $database->getConnection();
$message = new Message($db);

// Traitement du changement de statut
$status_update_message = '';
if(isset($_POST['update_status']) && isset($_POST['message_id']) && isset($_POST['status'])) {
    $message->id = $_POST['message_id'];
    $message->status = $_POST['status'];
    
    if($message->updateStatus()) {
        $status_update_message = '<div class="alert alert-success">Statut mis à jour avec succès!</div>';
    } else {
        $status_update_message = '<div class="alert alert-danger">Impossible de mettre à jour le statut!</div>';
    }
}

// Récupérer tous les messages
$result = $message->read();
$messages = [];
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $messages[] = $row;
}

// Définir le titre de la page
$pageTitle = "Gestion des messages";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Clyptor Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
        }
        .admin-sidebar a {
            color: #f8f9fa;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            transition: background-color 0.3s;
        }
        .admin-sidebar a:hover, .admin-sidebar a.active {
            background-color: #495057;
        }
        .admin-content {
            padding: 20px;
        }
        .message-card {
            margin-bottom: 20px;
            border-left: 5px solid #007bff;
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .message-body {
            padding: 15px;
        }
        .message-footer {
            padding: 10px 15px;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .status-new { background-color: #d4edda; border-left-color: #28a745; }
        .status-read { background-color: #fff3cd; border-left-color: #ffc107; }
        .status-replied { background-color: #d1ecf1; border-left-color: #17a2b8; }
        .status-closed { background-color: #f8d7da; border-left-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar">
                <h4 class="text-center mb-4">Admin Panel</h4>
                <div class="list-group">
                    <a href="../../../index.php?page=admin" class="list-group-item"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                    <a href="../../../index.php?page=user&action=list" class="list-group-item"><i class="fas fa-users me-2"></i> Users</a>
                    <a href="messages.php" class="list-group-item active"><i class="fas fa-envelope me-2"></i> Messages</a>
                    <a href="#" class="list-group-item"><i class="fas fa-car me-2"></i> Cars</a>
                    <a href="#" class="list-group-item"><i class="fas fa-home me-2"></i> Homes</a>
                    <a href="#" class="list-group-item"><i class="fas fa-road me-2"></i> Carpooling</a>
                    <a href="#" class="list-group-item"><i class="fas fa-cog me-2"></i> Settings</a>
                    <a href="../../../index.php?page=user&action=logout" class="list-group-item text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 admin-content">
                <h2 class="mb-4"><i class="fas fa-envelope me-2"></i> Messages de contact</h2>
                
                <?php echo $status_update_message; ?>
                
                <?php if(empty($messages)): ?>
                    <div class="alert alert-info">Aucun message de contact trouvé.</div>
                <?php else: ?>
                    <div class="mb-3">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary filter-btn active" data-filter="all">Tous (<?php echo count($messages); ?>)</button>
                            <button type="button" class="btn btn-outline-success filter-btn" data-filter="Nouveau">Nouveaux</button>
                            <button type="button" class="btn btn-outline-warning filter-btn" data-filter="Lu">Lus</button>
                            <button type="button" class="btn btn-outline-info filter-btn" data-filter="Répondu">Répondus</button>
                            <button type="button" class="btn btn-outline-danger filter-btn" data-filter="Clôturé">Clôturés</button>
                        </div>
                    </div>
                    
                    <div class="messages-container">
                        <?php foreach($messages as $msg): ?>
                            <?php
                            $statusClass = '';
                            switch($msg['status']) {
                                case 'Nouveau': $statusClass = 'status-new'; break;
                                case 'Lu': $statusClass = 'status-read'; break;
                                case 'Répondu': $statusClass = 'status-replied'; break;
                                case 'Clôturé': $statusClass = 'status-closed'; break;
                            }
                            ?>
                            <div class="card message-card <?php echo $statusClass; ?>" data-status="<?php echo $msg['status']; ?>">
                                <div class="message-header">
                                    <h5 class="mb-0"><?php echo htmlspecialchars($msg['subject']); ?></h5>
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($msg['status']); ?></span>
                                </div>
                                <div class="message-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>De:</strong> <?php echo htmlspecialchars($msg['name']); ?> (<?php echo htmlspecialchars($msg['email']); ?>)
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($msg['created'])); ?>
                                        </div>
                                    </div>
                                    <div class="message-content">
                                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                    </div>
                                </div>
                                <div class="message-footer">
                                    <div>
                                        <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-reply me-1"></i> Répondre
                                        </a>
                                    </div>
                                    <div>
                                        <form class="d-inline-block status-form" method="POST">
                                            <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                            <input type="hidden" name="update_status" value="1">
                                            <select name="status" class="form-select form-select-sm status-select">
                                                <option value="Nouveau" <?php echo $msg['status'] == 'Nouveau' ? 'selected' : ''; ?>>Nouveau</option>
                                                <option value="Lu" <?php echo $msg['status'] == 'Lu' ? 'selected' : ''; ?>>Lu</option>
                                                <option value="Répondu" <?php echo $msg['status'] == 'Répondu' ? 'selected' : ''; ?>>Répondu</option>
                                                <option value="Clôturé" <?php echo $msg['status'] == 'Clôturé' ? 'selected' : ''; ?>>Clôturé</option>
                                            </select>
                                            <button type="submit" class="btn btn-success btn-sm ms-2">Mettre à jour</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filtrage des messages par statut
            const filterButtons = document.querySelectorAll('.filter-btn');
            const messages = document.querySelectorAll('.message-card');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Mettre à jour les boutons actifs
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-filter');
                    
                    messages.forEach(message => {
                        if (filter === 'all' || message.getAttribute('data-status') === filter) {
                            message.style.display = 'block';
                        } else {
                            message.style.display = 'none';
                        }
                    });
                });
            });
            
            // Confirmer la mise à jour du statut
            const statusForms = document.querySelectorAll('.status-form');
            statusForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Êtes-vous sûr de vouloir modifier le statut de ce message?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html> 