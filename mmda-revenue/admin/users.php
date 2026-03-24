<?php
// mmda-revenue/admin/users.php
require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

require_role('Admin');

// Handle actions
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND id != ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    redirect('users.php', 'User deleted successfully.');
}

// Fetch all users
$stmt = $pdo->prepare("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.id DESC");
$stmt->execute();
$users = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-3">
        <h2>User Management</h2>
        <a href="../public/register.php" class="btn btn-primary">Add New User</a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo $user['phone']; ?></td>
                                <td><span class="badge bg-secondary"><?php echo $user['role_name']; ?></span></td>
                                <td><?php echo $user['created_at']; ?></td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                    <?php else: ?>
                                        <span class="text-muted small">You (Self)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
