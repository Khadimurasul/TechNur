<?php
// mmda-revenue/user/pay_bill.php
require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

require_role('Citizen');

$user_id = $_SESSION['user_id'];
$bill_id = $_GET['id'] ?? null;

if (!$bill_id) {
    redirect('dashboard.php', 'No bill selected.', 'danger');
}

// Fetch bill details
$stmt = $pdo->prepare("SELECT * FROM bills WHERE id = ? AND user_id = ? AND status = 'unpaid'");
$stmt->execute([$bill_id, $user_id]);
$bill = $stmt->fetch();

if (!$bill) {
    redirect('dashboard.php', 'Invalid bill or already paid.', 'danger');
}

include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h3>Mobile Money Payment</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Bill Type:</strong> <?php echo $bill['bill_type']; ?><br>
                    <strong>Amount Due:</strong> <?php echo format_currency($bill['amount']); ?><br>
                    <strong>Due Date:</strong> <?php echo $bill['due_date']; ?>
                </div>

                <div class="mt-4 p-4 border rounded bg-light">
                    <h5 class="mb-3">MoMo Simulation</h5>
                    <p class="text-muted">In a real system, this would redirect you to a payment provider like Paystack or Hubtel.</p>

                    <form action="process_payment.php" method="post" id="momoForm">
                        <input type="hidden" name="bill_id" value="<?php echo $bill['id']; ?>">
                        <input type="hidden" name="amount" value="<?php echo $bill['amount']; ?>">

                        <div class="mb-3">
                            <label for="momo_network" class="form-label">Network</label>
                            <select name="momo_network" class="form-select" required>
                                <option value="MTN">MTN MoMo</option>
                                <option value="Telecel">Telecel Cash</option>
                                <option value="AT">AT Money</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">MoMo Number</label>
                            <input type="text" name="phone" id="phone" class="form-control" required value="<?php echo $_SESSION['phone']; ?>">
                        </div>

                        <div class="alert alert-warning" id="prompt-alert" style="display:none;">
                            <strong>Wait!</strong> A MoMo prompt has been sent to your phone. Approve it to complete the transaction.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-warning" id="sendPrompt">Send MoMo Prompt</button>

                            <div id="simulatedActions" style="display:none;" class="mt-3">
                                <div class="card border-warning">
                                    <div class="card-body bg-dark text-white rounded">
                                        <p class="mb-2"><strong>[Simulated Phone Screen]</strong></p>
                                        <p>Approve payment of <?php echo format_currency($bill['amount']); ?> to MMDA?</p>
                                        <div class="d-flex gap-2">
                                            <button type="submit" name="action" value="approve" class="btn btn-sm btn-success flex-fill">Approve (Enter PIN)</button>
                                            <button type="submit" name="action" value="decline" class="btn btn-sm btn-danger flex-fill">Decline</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-footer">
                <a href="dashboard.php" class="btn btn-link">Cancel</a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('sendPrompt').addEventListener('click', function() {
    this.disabled = true;
    this.innerText = 'Prompt Sent...';
    document.getElementById('prompt-alert').style.display = 'block';
    document.getElementById('simulatedActions').style.display = 'block';
});
</script>

<?php include '../includes/footer.php'; ?>
