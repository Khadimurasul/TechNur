<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMDA Revenue Collection System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
            color: #ec5b13 !important;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            padding-top: 1rem;
        }
        .sidebar .nav-link {
            color: #dee2e6;
        }
        .sidebar .nav-link:hover {
            color: #fff;
        }
        .card-header {
            background-color: #ec5b13;
            color: white;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #ec5b13;
            border-color: #ec5b13;
        }
        .btn-primary:hover {
            background-color: #d44d0e;
            border-color: #d44d0e;
        }
        .alert-success {
            background-color: #d1e7dd;
            border-color: #badbcc;
            color: #0f5132;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../public/index.php">MMDA Revenue</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <span class="nav-link text-light">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> (<?php echo $_SESSION['role_name']; ?>)</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo get_dashboard_url($_SESSION['role_name']); ?>">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../public/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../public/index.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../public/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['flash_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['flash_message']; unset($_SESSION['flash_message']); unset($_SESSION['flash_type']); ?>
                <button type="button" class="btn-close" data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php endif; ?>
