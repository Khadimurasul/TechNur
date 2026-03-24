<?php
// mmda-revenue/collector/offline_payment.php
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['record_payment'])) {
    $bill_id = $_POST['bill_id'];
    $amount = $_POST['amount'];
    $transaction_ref = 'OFFLINE-' . strtoupper(uniqid());
    $status = 'success';

    try {
        $pdo->beginTransaction();

        // Record payment
        $stmt = $pdo->prepare("INSERT INTO payments (user_id, bill_id, amount, status, transaction_ref) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $bill_id, $amount, $status, $transaction_ref]);
        $payment_id = $pdo->lastInsertId();

        // Update bill status
        $stmt = $pdo->prepare("UPDATE bills SET status = 'paid' WHERE id = ?");
        $stmt->execute([$bill_id]);

        // Generate receipt
        $receipt_number = generate_receipt_number();
        $stmt = $pdo->prepare("INSERT INTO receipts (payment_id, receipt_number) VALUES (?, ?)");
        $stmt->execute([$payment_id, $receipt_number]);

        $pdo->commit();
        redirect('dashboard.php', 'Offline payment recorded successfully!', 'success');

    } catch (Exception $e) {
        $pdo->rollBack();
        redirect('dashboard.php', 'Error recording payment: ' . $e->getMessage(), 'danger');
    }
}

include '../includes/header.php';
?>

<div class="row">
    <div class="col-12 text-center mt-5">
        <h2>Record Offline Payment</h2>
        <p class="text-muted"><?php echo htmlspecialchars($user['name']); ?> (<?php echo $user['phone']; ?>)</p>
        <hr>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="offline_payment.php?user_id=<?php echo $user_id; ?>" method="post">
                    <div class="mb-3">
                        <label for="bill_id" class="form-label">Select Bill to Pay</label>
                        <select name="bill_id" id="bill_id" class="form-select" required>
                            <option value="">Choose Bill...</option>
                            <?php foreach ($unpaid_bills as $bill): ?>
                                <option value="<?php echo $bill['id']; ?>" data-amount="<?php echo $bill['amount']; ?>">
                                    <?php echo $bill['bill_type']; ?> - <?php echo format_currency($bill['amount']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount Collected (GH₵)</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control" readonly required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="record_payment" class="btn btn-warning">Confirm Cash Received</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('bill_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const amount = selectedOption.getAttribute('data-amount');
    document.getElementById('amount').value = amount || '';
});
</script>

<div class="mt-4 text-center">
    <a href="dashboard.php" class="btn btn-outline-secondary">Back to Search</a>
</div>

<?php include '../includes/footer.php'; ?>
