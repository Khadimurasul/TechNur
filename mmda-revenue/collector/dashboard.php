<?php
// mmda-revenue/collector/dashboard.php
require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

require_role('Collector');

$search_query = $_GET['search'] ?? '';
$users = [];

if (!empty($search_query)) {
    $stmt = $pdo->prepare("SELECT id, name, phone FROM users WHERE role_id = 3 AND (phone LIKE ? OR name LIKE ?)");
    $stmt->execute(['%' . $search_query . '%', '%' . $search_query . '%']);
    $users = $stmt->fetchAll();
}

include '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2>Revenue Collector Dashboard</h2>
        <hr>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header">Search Citizen to Initiate Payment</div>
            <div class="card-body">
                <form action="dashboard.php" method="get" class="row g-2">
                    <div class="col-md-9">
                        <input type="text" name="search" class="form-control" placeholder="Search by phone number or name..." value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                    <div class="col-md-3 d-grid">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($search_query)): ?>
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5>Search Results</h5>
                <table class="table table-hover mt-3">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u['name']); ?></td>
                                <td><?php echo $u['phone']; ?></td>
                                <td>
                                    <a href="initiate_payment.php?user_id=<?php echo $u['id']; ?>" class="btn btn-sm btn-success">View Bills & Pay</a>
                                    <a href="offline_payment.php?user_id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-secondary">Record Offline</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No citizens found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 bg-info text-white">
            <div class="card-body">
                <h5>Pro-tip</h5>
                <p class="mb-0">You can collect payments directly from citizens in the field and sync them to the system.</p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
