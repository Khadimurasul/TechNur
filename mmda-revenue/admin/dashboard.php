<?php
// mmda-revenue/admin/dashboard.php
require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

require_role('Admin');

// Fetch total revenue
$stmt = $pdo->prepare("SELECT SUM(amount) as total FROM payments WHERE status = 'success'");
$stmt->execute();
$total_revenue = $stmt->fetch()['total'] ?? 0;

// Fetch transaction count
$stmt = $pdo->prepare("SELECT COUNT(id) as count FROM payments");
$stmt->execute();
$transaction_count = $stmt->fetch()['count'] ?? 0;

// Fetch user count
$stmt = $pdo->prepare("SELECT COUNT(id) as count FROM users");
$stmt->execute();
$user_count = $stmt->fetch()['count'] ?? 0;

// Fetch recent transactions
$stmt = $pdo->prepare("SELECT p.*, u.name as user_name, b.bill_type FROM payments p
                        JOIN users u ON p.user_id = u.id
                        JOIN bills b ON p.bill_id = b.id
                        ORDER BY p.created_at DESC LIMIT 5");
$stmt->execute();
$recent_transactions = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2>Admin Dashboard</h2>
        <hr>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white text-center p-3 shadow-sm border-0">
            <h3><?php echo format_currency($total_revenue); ?></h3>
            <p class="mb-0">Total Revenue Collected</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white text-center p-3 shadow-sm border-0">
            <h3><?php echo $transaction_count; ?></h3>
            <p class="mb-0">Total Transactions</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white text-center p-3 shadow-sm border-0">
            <h3><?php echo $user_count; ?></h3>
            <p class="mb-0">Registered Users</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Recent Transactions</span>
                <a href="reports.php" class="btn btn-sm btn-light">View All Reports</a>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>User</th>
                            <th>Bill Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_transactions as $tx): ?>
                            <tr>
                                <td><?php echo $tx['transaction_ref']; ?></td>
                                <td><?php echo htmlspecialchars($tx['user_name']); ?></td>
                                <td><?php echo $tx['bill_type']; ?></td>
                                <td><?php echo format_currency($tx['amount']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo ($tx['status'] == 'success') ? 'success' : 'danger'; ?>">
                                        <?php echo strtoupper($tx['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $tx['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <h5>User Management</h5>
                <p>Manage citizens and collectors</p>
                <a href="users.php" class="btn btn-primary">Go to Users</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <h5>Bill Management</h5>
                <p>Create and manage bill types</p>
                <a href="bills.php" class="btn btn-primary">Go to Bills</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <h5>Revenue Reports</h5>
                <p>Detailed reports and CSV export</p>
                <a href="reports.php" class="btn btn-primary">Go to Reports</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
