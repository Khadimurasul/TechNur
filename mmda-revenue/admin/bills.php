<?php
// mmda-revenue/admin/bills.php
require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

require_role('Admin');

// Handle actions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_bill'])) {
    $user_id = $_POST['user_id'];
    $bill_type = sanitize_input($_POST['bill_type']);
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];

    $stmt = $pdo->prepare("INSERT INTO bills (user_id, bill_type, amount, status, due_date) VALUES (?, ?, ?, 'unpaid', ?)");
    if ($stmt->execute([$user_id, $bill_type, $amount, $due_date])) {
        redirect('bills.php', 'Bill created successfully.');
    }
}

// Fetch all users for dropdown
$stmt = $pdo->prepare("SELECT id, name, phone FROM users WHERE role_id = 3 ORDER BY name ASC");
$stmt->execute();
$users = $stmt->fetchAll();

// Fetch all bills
$stmt = $pdo->prepare("SELECT b.*, u.name as user_name, u.phone FROM bills b JOIN users u ON b.user_id = u.id ORDER BY b.created_at DESC");
$stmt->execute();
$bills = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header">Create New Bill</div>
            <div class="card-body">
                <form action="bills.php" method="post">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Select Citizen</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Select Citizen...</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?> (<?php echo $u['phone']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="bill_type" class="form-label">Bill Type</label>
                        <select name="bill_type" class="form-select" required>
                            <option value="Property Rate">Property Rate</option>
                            <option value="Market Toll">Market Toll</option>
                            <option value="Business License">Business License</option>
                            <option value="Sanitation Fee">Sanitation Fee</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (GH₵)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="create_bill" class="btn btn-primary">Generate Bill</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header">All Bills</div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Bill ID</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bills as $bill): ?>
                            <tr>
                                <td>#<?php echo $bill['id']; ?></td>
                                <td><?php echo htmlspecialchars($bill['user_name']); ?></td>
                                <td><?php echo $bill['bill_type']; ?></td>
                                <td><?php echo format_currency($bill['amount']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo ($bill['status'] == 'paid') ? 'success' : 'warning text-dark'; ?>">
                                        <?php echo strtoupper($bill['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $bill['due_date']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
