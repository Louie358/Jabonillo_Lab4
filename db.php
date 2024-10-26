<?php
$host = 'localhost';
$user = 'root'; // Default user for XAMPP/WAMP
$password = ''; // Default password
$dbname = 'Lab4_db';

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
