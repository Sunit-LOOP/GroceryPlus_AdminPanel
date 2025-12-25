<?php
session_start();
include '../db_config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Fetch users
$stmt = $pdo->query("SELECT * FROM users ORDER BY user_id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-users"></i>
        Manage Users
    </h1>
    <div class="page-actions">
        <span class="text-muted"><?php echo count($users); ?> total users</span>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list"></i> User Management</span>
            <div class="input-group" style="width: 300px;">
                <input type="text" class="form-control" id="searchInput" placeholder="Search users...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($users)): ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Users Found</h4>
                <p class="text-muted">No users have registered in the system yet.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-user"></i> Name</th>
                        <th><i class="fas fa-envelope"></i> Email</th>
                        <th><i class="fas fa-phone"></i> Phone</th>
                        <th><i class="fas fa-calendar"></i> Joined</th>
                        <th><i class="fas fa-cogs"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                     style="width: 35px; height: 35px; font-weight: bold;">
                                    <?php echo strtoupper(substr($user['user_name'], 0, 1)); ?>
                                </div>
                                <?php echo htmlspecialchars($user['user_name']); ?>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($user['user_email'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($user['user_phone'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars(date('M d, Y', strtotime($user['created_at']))); ?></td>
                        <td>
                            <a href="view_user.php?id=<?php echo $user['user_id']; ?>"
                               class="btn btn-sm btn-outline-primary" title="View Details">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#usersTable tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<?php include 'includes/footer.php'; ?>