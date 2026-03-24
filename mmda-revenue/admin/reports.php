<?php
// mmda-revenue/admin/reports.php
require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

require_role('Admin');

// Filter logic
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');
$bill_type = $_GET['bill_type'] ?? '';

$sql = "SELECT p.*, u.name as user_name, b.bill_type
        FROM payments p
        JOIN users u ON p.user_id = u.id
        JOIN bills b ON p.bill_id = b.id
        WHERE p.status = 'success'
        AND DATE(p.created_at) BETWEEN ? AND ?";

$params = [$start_date, $end_date];

if (!empty($bill_type)) {
    $sql .= " AND b.bill_type = ?";
    $params[] = $bill_type;
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll();

// Handle CSV Export
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="MMDA_Revenue_Report_' . time() . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Transaction Ref', 'User', 'Bill Type', 'Amount', 'Date']);
    foreach ($reports as $r) {
        fputcsv($output, [$r['id'], $r['transaction_ref'], $r['user_name'], $r['bill_type'], $r['amount'], $r['created_at']]);
    }
    fclose($output);
    exit();
}

include '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2>Revenue Reports</h2>
        <hr>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-light">
            <div class="card-body">
                <form action="reports.php" method="get" class="row g-3">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="bill_type" class="form-label">Bill Type</label>
                        <select name="bill_type" id="bill_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="Property Rate" <?php echo ($bill_type == 'Property Rate') ? 'selected' : ''; ?>>Property Rate</option>
                            <option value="Market Toll" <?php echo ($bill_type == 'Market Toll') ? 'selected' : ''; ?>>Market Toll</option>
                            <option value="Business License" <?php echo ($bill_type == 'Business License') ? 'selected' : ''; ?>>Business License</option>
                            <option value="Sanitation Fee" <?php echo ($bill_type == 'Sanitation Fee') ? 'selected' : ''; ?>>Sanitation Fee</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                        <a href="?<?php echo $_SERVER['QUERY_STRING']; ?>&export=1" class="btn btn-success flex-fill">Export CSV</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5>Filtered Results</h5>
                    <?php
                        $total = array_sum(array_column($reports, 'amount'));
                        echo "<strong>Total: " . format_currency($total) . "</strong>";
                    ?>
                </div>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>User</th>
                            <th>Bill Type</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $r): ?>
                            <tr>
                                <td><?php echo $r['transaction_ref']; ?></td>
                                <td><?php echo htmlspecialchars($r['user_name']); ?></td>
                                <td><?php echo $r['bill_type']; ?></td>
                                <td><?php echo format_currency($r['amount']); ?></td>
                                <td><?php echo $r['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($reports)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No results found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
