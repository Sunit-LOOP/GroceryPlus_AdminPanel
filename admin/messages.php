<?php
session_start();
include '../db_config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Handle new message from admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['message'])) {
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message_text, created_at) VALUES (?, ?, ?, datetime('now'))");
    $stmt->execute([1, $_POST['user_id'], $_POST['message']]); // Assuming admin user_id is 1
    header('Location: messages.php?sent=1');
    exit;
}

// Fetch all messages
$stmt = $pdo->query("SELECT m.*, u.user_name FROM messages m LEFT JOIN users u ON m.sender_id = u.user_id ORDER BY m.created_at DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group messages by user for conversations
$conversations = [];
foreach ($messages as $msg) {
    $user_id = $msg['sender_id'] == 1 ? $msg['receiver_id'] : $msg['sender_id']; // Assuming admin is user_id 1
    if ($user_id && !isset($conversations[$user_id])) {
        $conversations[$user_id] = [
            'user_name' => $msg['user_name'] ?? 'Unknown User',
            'last_message' => substr($msg['message_text'], 0, 50) . (strlen($msg['message_text']) > 50 ? '...' : ''),
            'last_message_time' => $msg['created_at'],
            'unread_count' => $msg['is_read'] == 0 && $msg['receiver_id'] == 1 ? 1 : 0
        ];
    }
}

include 'includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-comments"></i>
        Messages & Support
    </h1>
    <div class="page-actions">
        <span class="text-muted"><?php echo count($conversations); ?> active conversations</span>
    </div>
</div>

<?php if (isset($_GET['sent'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i>
    Message sent successfully!
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-users"></i>
                Conversations
            </div>
            <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                <?php if (empty($conversations)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No messages yet</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $user_id => $conv): ?>
                    <div class="conversation-item p-3 border-bottom" data-user-id="<?php echo $user_id; ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="fw-bold"><?php echo htmlspecialchars($conv['user_name']); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($conv['last_message']); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars(date('M d, H:i', strtotime($conv['last_message_time']))); ?></div>
                            </div>
                            <?php if ($conv['unread_count'] > 0): ?>
                            <span class="badge bg-danger"><?php echo $conv['unread_count']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card" id="chatCard" style="display: none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span id="chatTitle"><i class="fas fa-comments"></i> Select a conversation</span>
                <button class="btn btn-sm btn-outline-secondary" onclick="closeChat()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="card-body d-flex flex-column" style="height: 500px;">
                <div id="chatMessages" class="flex-grow-1 overflow-auto mb-3 p-3 border rounded" style="background: #f8f9fa;">
                    <!-- Messages will be loaded here -->
                </div>
                <form id="messageForm" method="POST" class="d-flex gap-2">
                    <input type="hidden" name="user_id" id="messageUserId">
                    <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="card" id="welcomeCard">
            <div class="card-body text-center py-5">
                <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Select a Conversation</h4>
                <p class="text-muted">Choose a user from the list to start chatting</p>
            </div>
        </div>
    </div>
</div>

<script>
// Conversation selection
document.querySelectorAll('.conversation-item').forEach(item => {
    item.addEventListener('click', function() {
        const userId = this.dataset.userId;
        const userName = this.querySelector('.fw-bold').textContent;

        // Update UI
        document.getElementById('chatTitle').innerHTML = `<i class="fas fa-comments"></i> Chat with ${userName}`;
        document.getElementById('messageUserId').value = userId;
        document.getElementById('welcomeCard').style.display = 'none';
        document.getElementById('chatCard').style.display = 'block';

        // Load messages (simplified - in real app you'd load via AJAX)
        loadMessages(userId);
    });
});

function closeChat() {
    document.getElementById('chatCard').style.display = 'none';
    document.getElementById('welcomeCard').style.display = 'block';
}

function loadMessages(userId) {
    // This is a simplified version. In a real app, you'd load messages via AJAX
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.innerHTML = '<div class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin"></i> Loading messages...</div>';

    // Simulate loading (replace with actual AJAX call)
    setTimeout(() => {
        chatMessages.innerHTML = '<div class="text-center text-muted py-3">Messages would be loaded here via AJAX</div>';
    }, 500);
}

// Auto-hide alert after 3 seconds
setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) {
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 150);
    }
}, 3000);
</script>

<style>
.conversation-item {
    cursor: pointer;
    transition: background-color 0.2s;
}
.conversation-item:hover {
    background-color: rgba(76, 175, 80, 0.05);
}
.conversation-item.active {
    background-color: rgba(76, 175, 80, 0.1);
    border-left: 3px solid var(--primary-color);
}
</style>

<?php include 'includes/footer.php'; ?>