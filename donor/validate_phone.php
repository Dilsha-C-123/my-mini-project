<?php
include '../config.php'; // Changed to go up one directory to root

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone_number'];
    
    // Remove any non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Validate Indian mobile number format
    if (preg_match("/^[6-9][0-9]{9}$/", $phone)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please enter a valid 10-digit mobile number starting with 6-9'
        ]);
    }
}
?> 