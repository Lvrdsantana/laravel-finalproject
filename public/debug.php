<?php
// Script de debug pour Railway

echo "<h1>Debug Info</h1>";

// Vérifier la base de données
echo "<h2>Database</h2>";
$dbPath = __DIR__ . '/../database/database.sqlite';
echo "DB Path: " . $dbPath . "<br>";
echo "DB Exists: " . (file_exists($dbPath) ? 'YES ✅' : 'NO ❌') . "<br>";
echo "DB Readable: " . (is_readable($dbPath) ? 'YES ✅' : 'NO ❌') . "<br>";
echo "DB Writable: " . (is_writable($dbPath) ? 'YES ✅' : 'NO ❌') . "<br>";
echo "DB Size: " . (file_exists($dbPath) ? filesize($dbPath) . ' bytes' : 'N/A') . "<br>";

// Vérifier storage
echo "<h2>Storage</h2>";
$storagePath = __DIR__ . '/../storage/framework/sessions';
echo "Sessions Path: " . $storagePath . "<br>";
echo "Sessions Exists: " . (file_exists($storagePath) ? 'YES ✅' : 'NO ❌') . "<br>";
echo "Sessions Writable: " . (is_writable($storagePath) ? 'YES ✅' : 'NO ❌') . "<br>";

// Vérifier les variables d'environnement
echo "<h2>Environment</h2>";
echo "APP_ENV: " . getenv('APP_ENV') . "<br>";
echo "APP_DEBUG: " . getenv('APP_DEBUG') . "<br>";
echo "APP_KEY: " . (getenv('APP_KEY') ? substr(getenv('APP_KEY'), 0, 20) . '...' : 'NOT SET ❌') . "<br>";
echo "DB_CONNECTION: " . getenv('DB_CONNECTION') . "<br>";
echo "SESSION_DRIVER: " . getenv('SESSION_DRIVER') . "<br>";

// Tester la connexion DB
echo "<h2>Database Connection Test</h2>";
try {
    $db = new PDO('sqlite:' . $dbPath);
    $result = $db->query("SELECT COUNT(*) as count FROM users");
    $count = $result->fetch(PDO::FETCH_ASSOC);
    echo "Users count: " . $count['count'] . " ✅<br>";
    
    $result = $db->query("SELECT name, email, role FROM users LIMIT 3");
    echo "<h3>Sample Users:</h3>";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['name'] . " (" . $row['email'] . ") - " . $row['role'] . "<br>";
    }
} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage() . " ❌<br>";
}

echo "<h2>PHP Info</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Extensions: " . implode(', ', get_loaded_extensions()) . "<br>";

