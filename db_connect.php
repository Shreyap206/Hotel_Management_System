<?php
/**
 * Database Connection File
 * Modify these settings according to your local/production database configuration.
 */

$host = 'localhost';
$port = '3307'; // Port captured from your XAMPP Control Panel
$username = 'root'; // default XAMPP username
$password = ''; // default XAMPP password is empty
$database = 'hotel_db'; // Change this to your project database name

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Uncomment the line below to check connection (useful for debugging)
    // echo "Connected successfully"; 
} catch(PDOException $e) {
    // If you haven't created the database yet, this will fail gracefully.
    // die("Connection failed: " . $e->getMessage());
    $db_connection_error = "Database connection failed. Please ensure your database is created and configured correctly.";
}
?>
