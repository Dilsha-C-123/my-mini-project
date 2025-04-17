<?php
$servername = "localhost";
$username = "root"; // Change if your MySQL username is different
$password = ""; // Change if you have a MySQL password
$dbname = "food_management"; // Make sure this matches your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
