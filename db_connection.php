<?php
// db_connection.php - Include this in your main files

// --- Database Configuration ---
 // Your MySQL password

$host = 'localhost';
$dbname = 'menu';
$username = 'root';
$password = '';
$charset  = 'utf8mb4';


// --- Establish Connection (PDO) ---
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Turn on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch as arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    // If connection fails, stop everything.
    // In production, log this error instead of echoing it.
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>