<?php
// mmda-revenue/collector/initiate_payment.php
require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

require_role('Collector');

$user_id = $_GET['user_id'] ?? null;
if (!$user_id) {
    redirect('dashboard.php', 'No citizen selected.', 'danger');
}

// Fetch user details
$stmt = $pdo->prepare("SELECT name, phone FROM users WHERE id = ? AND role_id = 3");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    redirect('dashboard.php', 'Invalid citizen.', 'danger');
}

// Fetch unpaid bills
$stmt = $pdo->prepare("SELECT * FROM bills WHERE user_id = ? AND status = 'unpaid' ORDER BY due_date ASC");
$stmt->execute([$user_id]);
$unpaid_bills = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2>Initiate Payment for Citizen</h2>
        <p class="text-muted"><?php echo htmlspecialchars($user['name']); ?> (<?php echo $user['phone']; ?>)</p>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">Unpaid Bills</div>
            <div class="card-body">
                <?php if (count($unpaid_bills) > 0): ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Bill ID</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unpaid_bills as $bill): ?>
                                <tr>
                                    <td>#<?php echo $bill['id']; ?></td>
                                    <td><?php echo $bill['bill_type']; ?></td>
                                    <td><?php echo format_currency($bill['amount']); ?></td>
                                    <td><?php echo $bill['due_date']; ?></td>
                                    <td>
                                        <form action="../user/process_payment.php" method="post" class="d-inline">
                                            <input type="hidden" name="bill_id" value="<?php echo $bill['id']; ?>">
                                            <input type="hidden" name="amount" value="<?php echo $bill['amount']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-sm btn-success">Pay with MoMo</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No unpaid bills found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="dashboard.php" class="btn btn-outline-secondary">Back to Search</a>
</div>

<?php include '../includes/footer.php'; ?>
