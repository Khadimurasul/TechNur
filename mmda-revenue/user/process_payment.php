<?php
// mmda-revenue/user/process_payment.php
require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

require_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bill_id = $_POST['bill_id'];
    $amount = $_POST['amount'];
    $action = $_POST['action'] ?? 'decline';

    // Check if payment already exists for this bill (success only)
    $stmt = $pdo->prepare("SELECT id FROM payments WHERE bill_id = ? AND status = 'success'");
    $stmt->execute([$bill_id]);
    if ($stmt->fetch()) {
        redirect('dashboard.php', 'Bill already paid.', 'info');
    }

    $transaction_ref = generate_transaction_ref();
    $status = ($action === 'approve') ? 'success' : 'failed';

    try {
        $pdo->beginTransaction();

        // Get the user_id associated with the bill
        $stmt = $pdo->prepare("SELECT user_id FROM bills WHERE id = ?");
        $stmt->execute([$bill_id]);
        $bill_user = $stmt->fetch();
        $bill_user_id = $bill_user['user_id'];

        // Record payment
        $stmt = $pdo->prepare("INSERT INTO payments (user_id, bill_id, amount, status, transaction_ref) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$bill_user_id, $bill_id, $amount, $status, $transaction_ref]);
        $payment_id = $pdo->lastInsertId();

        if ($status === 'success') {
            // Update bill status
            $stmt = $pdo->prepare("UPDATE bills SET status = 'paid' WHERE id = ?");
            $stmt->execute([$bill_id]);

            // Generate receipt
            $receipt_number = generate_receipt_number();
            $stmt = $pdo->prepare("INSERT INTO receipts (payment_id, receipt_number) VALUES (?, ?)");
            $stmt->execute([$payment_id, $receipt_number]);

            $pdo->commit();
            redirect('receipt.php?id=' . $payment_id, 'Payment successful!', 'success');
        } else {
            $pdo->commit();
            redirect('dashboard.php', 'Payment was declined or failed.', 'danger');
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        redirect('dashboard.php', 'Error processing payment: ' . $e->getMessage(), 'danger');
    }
} else {
    redirect('dashboard.php');
}
?>
