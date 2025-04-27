<?php
$servername = "localhost"; // Default for XAMPP
$username = "root"; // Default for XAMPP
$password = ""; // Default for XAMPP
$dbname = "skmd_db"; // The database you created

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
