<?php
// mmda-revenue/public/register.php
require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    header("Location: " . get_dashboard_url($_SESSION['role_name']));
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = sanitize_input($_POST['name']);
    $phone = sanitize_input($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if phone already exists
        $sql = "SELECT id FROM users WHERE phone = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$phone]);
        if ($stmt->fetch()) {
            $error = "Phone number already registered.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user (default role is Citizen, role_id = 3)
            $sql = "INSERT INTO users (name, phone, password, role_id) VALUES (?, ?, ?, 3)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$name, $phone, $hashed_password])) {
                $_SESSION['flash_message'] = "Registration successful. Please login.";
                $_SESSION['flash_type'] = "success";
                header("Location: index.php");
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card mt-5 shadow">
            <div class="card-header text-center">
                <h3>Register</h3>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" required placeholder="Enter full name">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-control" required placeholder="024XXXXXXX">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="Create password">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required placeholder="Confirm password">
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="register" class="btn btn-primary">Register</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                Already have an account? <a href="index.php">Login here</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
