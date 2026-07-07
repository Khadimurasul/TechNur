<?php
/**
 * db_setup.php
 * Initializes the SQLite database and creates the necessary tables for the
 * Puzuri Farms Weather Forecast System.
 */

$db_dir = __DIR__ . '/data';
$db_file = $db_dir . '/puzuri.db';

// Ensure the data directory exists
if (!is_dir($db_dir)) {
    mkdir($db_dir, 0777, true);
}

try {
    // Create (or open) the SQLite database
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the weather_logs table
    $sql = "CREATE TABLE IF NOT EXISTS weather_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        temperature REAL,
        humidity INTEGER,
        location TEXT,
        wind_speed REAL,
        description TEXT
    )";

    $pdo->exec($sql);

    echo "Database initialized successfully at: " . $db_file . "\n";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
