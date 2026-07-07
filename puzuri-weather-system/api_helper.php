<?php
/**
 * api_helper.php
 * Handles OpenWeatherMap API requests, farming advice logic, and database logging.
 */

// Replace with your actual OpenWeatherMap API key
define('OWM_API_KEY', 'YOUR_API_KEY_HERE');
define('DEFAULT_CITY', 'Tamale,GH');
define('DB_PATH', __DIR__ . '/data/puzuri.db');

/**
 * Fetches current weather data for a given city.
 */
function fetchCurrentWeather($city = DEFAULT_CITY) {
    $url = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid=" . OWM_API_KEY . "&units=metric";
    return makeApiRequest($url);
}

/**
 * Fetches 5-day forecast data for a given city.
 */
function fetchForecast($city = DEFAULT_CITY) {
    $url = "https://api.openweathermap.org/data/2.5/forecast?q=" . urlencode($city) . "&appid=" . OWM_API_KEY . "&units=metric";
    return makeApiRequest($url);
}

/**
 * General function to make API requests using file_get_contents or cURL.
 */
function makeApiRequest($url) {
    // For testing/mocking purposes when no API key is provided
    if (OWM_API_KEY === 'YOUR_API_KEY_HERE' || empty(OWM_API_KEY)) {
        return getMockData($url);
    }

    $response = @file_get_contents($url);
    if ($response === FALSE) {
        return ['error' => 'Unable to fetch weather data. Check your API key or city name.'];
    }
    return json_decode($response, true);
}

/**
 * Logs weather data into the SQLite database.
 */
function logWeatherData($data) {
    if (isset($data['error'])) return;

    try {
        $pdo = new PDO("sqlite:" . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO weather_logs (temperature, humidity, location, wind_speed, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['main']['temp'],
            $data['main']['humidity'],
            $data['name'] ?? 'Unknown',
            $data['wind']['speed'],
            $data['weather'][0]['description']
        ]);
    } catch (PDOException $e) {
        // Silently fail or log to a file in production
        error_log("DB Log Error: " . $e->getMessage());
    }
}

/**
 * Farming Advice Engine
 * Translates weather parameters into actionable advice for Puzuri Farms.
 */
function getFarmingAdvice($weather) {
    $advice = [];
    $temp = $weather['main']['temp'];
    $humidity = $weather['main']['humidity'];
    $wind_speed = $weather['wind']['speed'] * 3.6; // Convert m/s to km/h
    $rain = isset($weather['rain']['1h']) ? $weather['rain']['1h'] : (isset($weather['rain']['3h']) ? $weather['rain']['3h'] : 0);

    // Maize Advice
    if ($rain > 10) {
        $advice['Maize'] = "Ideal for planting Maize due to significant rainfall forecast.";
    } elseif ($temp > 35) {
        $advice['Maize'] = "High heat alert. Monitor moisture levels for Maize.";
    } else {
        $advice['Maize'] = "Conditions are stable for Maize growth.";
    }

    // Rice Advice
    if ($humidity > 85) {
        $advice['Rice'] = "Warning: High fungal risk for Rice. Consider preventive measures.";
    } elseif ($rain > 5) {
        $advice['Rice'] = "Good hydration levels for Rice paddies.";
    } else {
        $advice['Rice'] = "Maintain standard irrigation for Rice.";
    }

    // Soybean & Sorghum Advice
    if ($wind_speed > 15) {
        $advice['General'] = "Wind speed > 15km/h: Postpone pesticide spraying to avoid drift.";
    }

    if ($temp < 20) {
        $advice['Soybean'] = "Low temperature may slow Soybean germination.";
    } else {
        $advice['Soybean'] = "Optimal temperature for Soybean development.";
    }

    $advice['Sorghum'] = ($temp > 30 && $rain < 2) ? "Sorghum's drought tolerance is an advantage today." : "Sorghum growth is progressing normally.";

    return $advice;
}

/**
 * Provides mock data for development and testing without an API key.
 */
function getMockData($url) {
    if (strpos($url, 'forecast') !== false) {
        return [
            'list' => array_fill(0, 5, [
                'dt' => time() + 86400,
                'main' => ['temp' => 32, 'humidity' => 60],
                'weather' => [['description' => 'scattered clouds', 'icon' => '03d']],
                'wind' => ['speed' => 4.1]
            ]),
            'city' => ['name' => 'Tamale (Mock)']
        ];
    } else {
        return [
            'main' => ['temp' => 30.5, 'humidity' => 88],
            'name' => 'Tamale (Mock)',
            'wind' => ['speed' => 5.5], // 19.8 km/h
            'weather' => [['description' => 'light rain', 'icon' => '10d']],
            'rain' => ['1h' => 12]
        ];
    }
}
