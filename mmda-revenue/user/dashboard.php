<?php
// mmda-revenue/user/dashboard.php
require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

require_role('Citizen');

$user_id = $_SESSION['user_id'];

// Fetch unpaid bills
$stmt = $pdo->prepare("SELECT * FROM bills WHERE user_id = ? AND status = 'unpaid' ORDER BY due_date ASC");
$stmt->execute([$user_id]);
$unpaid_bills = $stmt->fetchAll();

// Fetch payment history
$stmt = $pdo->prepare("SELECT p.*, b.bill_type, r.receipt_number FROM payments p
                        JOIN bills b ON p.bill_id = b.id
                        LEFT JOIN receipts r ON p.id = r.payment_id
                        WHERE p.user_id = ? ORDER BY p.created_at DESC");
$stmt->execute([$user_id]);
$payment_history = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2>Citizen Dashboard</h2>
        <hr>
    </div>
</div>

<div class="row">
    <!-- Unpaid Bills -->
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">Unpaid Bills</div>
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
                                        <a href="pay_bill.php?id=<?php echo $bill['id']; ?>" class="btn btn-sm btn-success">Pay with MoMo</a>
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

    <!-- Payment History -->
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header">Payment History</div>
            <div class="card-body">
                <?php if (count($payment_history) > 0): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Ref</th>
                                <th>Bill Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payment_history as $payment): ?>
                                <tr>
                                    <td><?php echo $payment['transaction_ref']; ?></td>
                                    <td><?php echo $payment['bill_type']; ?></td>
                                    <td><?php echo format_currency($payment['amount']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo ($payment['status'] == 'success') ? 'success' : 'danger'; ?>">
                                            <?php echo strtoupper($payment['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $payment['created_at']; ?></td>
                                    <td>
                                        <?php if ($payment['status'] == 'success'): ?>
                                            <a href="receipt.php?id=<?php echo $payment['id']; ?>" class="btn btn-sm btn-outline-primary">View Receipt</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No payment history found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
