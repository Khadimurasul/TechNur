<?php
/**
 * index.php
 * Main Dashboard for Puzuri Farms Weather Forecast System.
 */

require_once 'api_helper.php';

$city = isset($_GET['city']) ? $_GET['city'] : DEFAULT_CITY;
$current_weather = fetchCurrentWeather($city);
$forecast = fetchForecast($city);

// Log data if successful
if (!isset($current_weather['error'])) {
    logWeatherData($current_weather);
    $advice = getFarmingAdvice($current_weather);
} else {
    $advice = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puzuri Farms | Weather Forecast Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom Style -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-cloud-sun-fill me-2"></i> PUZURI FARMS WEATHER
            </a>
        </div>
    </nav>

    <div class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Tamale Agricultural Intelligence</h1>
            <p class="lead">Real-time weather insights for Maize, Rice, Soybean, and Sorghum.</p>

            <div class="row justify-content-center mt-4">
                <div class="col-md-6">
                    <form action="index.php" method="GET" class="d-flex">
                        <input type="text" name="city" class="form-control form-control-lg me-2" placeholder="Enter city name (e.g. Tamale, GH)" value="<?php echo htmlspecialchars($city); ?>">
                        <button type="submit" class="btn btn-warning btn-lg px-4">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if (isset($current_weather['error'])): ?>
            <div class="alert alert-danger shadow-sm">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $current_weather['error']; ?>
            </div>
        <?php else: ?>
            <div class="row mb-4">
                <!-- Current Weather Card -->
                <div class="col-lg-4">
                    <div class="card h-100 p-4 text-center">
                        <h3 class="text-muted mb-0"><?php echo $current_weather['name']; ?></h3>
                        <p class="text-secondary small"><?php echo date('l, jS F Y'); ?></p>
                        <img src="https://openweathermap.org/img/wn/<?php echo $current_weather['weather'][0]['icon']; ?>@2x.png" alt="Weather Icon" class="weather-icon mx-auto">
                        <h2 class="display-3 fw-bold mb-0"><?php echo round($current_weather['main']['temp']); ?>°C</h2>
                        <p class="text-capitalize fs-4"><?php echo $current_weather['weather'][0]['description']; ?></p>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-0 text-muted small">HUMIDITY</p>
                                <h5 class="fw-bold"><?php echo $current_weather['main']['humidity']; ?>%</h5>
                            </div>
                            <div class="col-6">
                                <p class="mb-0 text-muted small">WIND</p>
                                <h5 class="fw-bold"><?php echo round($current_weather['wind']['speed'] * 3.6, 1); ?> km/h</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advice Cards -->
                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card h-100 advice-card p-3">
                                <h5 class="fw-bold text-success"><i class="bi bi-tree-fill me-2"></i> Maize Advice</h5>
                                <p><?php echo $advice['Maize'] ?? 'No specific advice available.'; ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 advice-card p-3">
                                <h5 class="fw-bold text-success"><i class="bi bi-water me-2"></i> Rice Advice</h5>
                                <p><?php echo $advice['Rice'] ?? 'No specific advice available.'; ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 advice-card p-3">
                                <h5 class="fw-bold text-success"><i class="bi bi-flower1 me-2"></i> Soybean</h5>
                                <p><?php echo $advice['Soybean'] ?? 'No specific advice available.'; ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 advice-card p-3">
                                <h5 class="fw-bold text-success"><i class="bi bi-sun-fill me-2"></i> Sorghum</h5>
                                <p><?php echo $advice['Sorghum'] ?? 'No specific advice available.'; ?></p>
                            </div>
                        </div>
                        <?php if (isset($advice['General'])): ?>
                        <div class="col-12">
                            <div class="card bg-warning-subtle border-warning p-3">
                                <h5 class="fw-bold text-warning-emphasis"><i class="bi bi-exclamation-octagon-fill me-2"></i> Farm Warning</h5>
                                <p class="mb-0"><?php echo $advice['General']; ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Forecast Section -->
            <div class="mt-5">
                <h3 class="fw-bold mb-4">5-Day Agricultural Outlook</h3>
                <div class="row g-2">
                    <?php
                    $daily_forecast = [];
                    if (isset($forecast['list'])) {
                        foreach ($forecast['list'] as $item) {
                            $date = date('Y-m-d', $item['dt']);
                            if (!isset($daily_forecast[$date])) {
                                $daily_forecast[$date] = $item;
                            }
                            if (count($daily_forecast) >= 5) break;
                        }
                    }

                    foreach ($daily_forecast as $day):
                    ?>
                    <div class="col">
                        <div class="forecast-item shadow-sm">
                            <p class="fw-bold mb-1"><?php echo date('D, M j', $day['dt']); ?></p>
                            <img src="https://openweathermap.org/img/wn/<?php echo $day['weather'][0]['icon']; ?>.png" alt="Icon">
                            <h5 class="mb-0"><?php echo round($day['main']['temp']); ?>°C</h5>
                            <small class="text-muted"><?php echo $day['main']['humidity']; ?>% Hum.</small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer class="text-center">
        <div class="container">
            <p class="mb-1">&copy; <?php echo date('Y'); ?> Puzuri Farms - Tamale, Northern Ghana.</p>
            <small>Data powered by OpenWeatherMap API | Designed for Local Prosperity.</small>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
