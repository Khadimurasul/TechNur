<?php
// mmda-revenue/user/receipt.php
require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

require_login();

$payment_id = $_GET['id'] ?? null;
if (!$payment_id) {
    redirect('dashboard.php', 'No receipt found.', 'danger');
}

// Fetch receipt details
$sql = "SELECT r.*, p.amount, p.transaction_ref, p.created_at as paid_at, p.user_id, b.bill_type, u.name as user_name, u.phone
        FROM receipts r
        JOIN payments p ON r.payment_id = p.id
        JOIN bills b ON p.bill_id = b.id
        JOIN users u ON p.user_id = u.id
        WHERE p.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$payment_id]);
$receipt = $stmt->fetch();

if (!$receipt) {
    redirect('dashboard.php', 'Receipt not found.', 'danger');
}

// Security: Check if user owns this receipt (Citizens only)
if (has_role('Citizen') && $receipt['user_id'] != $_SESSION['user_id']) {
    redirect('dashboard.php', 'Unauthorized receipt access.', 'danger');
}

include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow border-0" id="receiptContent">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">OFFICIAL RECEIPT</h2>
                    <p class="text-muted">Metropolitan, Municipal, and District Assemblies (MMDAs) - Ghana</p>
                </div>

                <hr>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <h6 class="text-muted mb-1">Receipt Number:</h6>
                        <p class="fw-bold"><?php echo $receipt['receipt_number']; ?></p>

                        <h6 class="text-muted mb-1">Transaction Ref:</h6>
                        <p><?php echo $receipt['transaction_ref']; ?></p>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <h6 class="text-muted mb-1">Date Paid:</h6>
                        <p><?php echo $receipt['paid_at']; ?></p>

                        <h6 class="text-muted mb-1">Status:</h6>
                        <p class="text-success fw-bold">PAID (MoMo)</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-12">
                        <h6 class="text-muted mb-1">Paid By:</h6>
                        <p class="mb-0"><?php echo htmlspecialchars($receipt['user_name']); ?></p>
                        <p><?php echo $receipt['phone']; ?></p>
                    </div>
                </div>

                <table class="table table-bordered mb-4">
                    <thead class="bg-light">
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $receipt['bill_type']; ?></td>
                            <td class="text-end"><?php echo format_currency($receipt['amount']); ?></td>
                        </tr>
                        <tr>
                            <th class="text-end">Total Paid</th>
                            <th class="text-end"><?php echo format_currency($receipt['amount']); ?></th>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-5 text-center">
                    <p class="text-muted small">This is an electronically generated receipt. No signature required.</p>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <button onclick="window.print()" class="btn btn-primary me-2">Print Receipt</button>
            <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
