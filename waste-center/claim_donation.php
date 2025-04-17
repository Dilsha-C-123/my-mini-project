<?php
include '../config.php'; // Ensure correct path

session_start();

if (!isset($_SESSION['waste_center'])) {
    header("Location: waste_center_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: waste_center_dashboard.php");
    exit();
}

$donation_id = $_GET['id'];
$waste_center = $_SESSION['waste_center'];

// Update the donation status to "Claimed" and associate it with the waste center
$update_sql = "UPDATE donations SET status = 'Claimed' WHERE id = ?";
$stmt = $conn->prepare($update_sql);
$stmt->bind_param("i", $donation_id);
$stmt->execute();

// Insert a record into the claims table
$insert_sql = "INSERT INTO claims (donation_id, waste_center) VALUES (?, ?)";
$stmt = $conn->prepare($insert_sql);
$stmt->bind_param("is", $donation_id, $waste_center);
$stmt->execute();

// Redirect back to the dashboard
header("Location: waste_center_dashboard.php");
exit();
?>