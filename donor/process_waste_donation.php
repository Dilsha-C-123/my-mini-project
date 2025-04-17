<?php
include '../config.php';
require 'send_email.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['donor_name'], $_POST['contact_number'], $_POST['email'], $_POST['food_type'], 
              $_POST['quantity'], $_POST['location'], $_POST['payment_mode'])) {
        
        // Validate phone number
        $contact_number = preg_replace('/[^0-9]/', '', $_POST['contact_number']);
        if (!preg_match("/^[6-9][0-9]{9}$/", $contact_number)) {
            echo "Invalid phone number format";
            exit;
        }

        // Add email validation
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            echo "Invalid email format";
            exit;
        }

        $donor_name = $conn->real_escape_string($_POST['donor_name']);
        $food_type = $conn->real_escape_string($_POST['food_type']);
        $quantity = $conn->real_escape_string($_POST['quantity']);
        if ($quantity < 3) {
            echo "Minimum donation quantity is 3 kg";
            exit;
        }
        $location = $conn->real_escape_string($_POST['location']);
        $payment_mode = $conn->real_escape_string($_POST['payment_mode']);
        
        // Calculate charges
        $charges = $quantity * 10; // ₹10 per kg

        // Handle payment proof upload
        $payment_proof = '';
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['payment_proof'];
            $file_name = time() . '_' . $file['name'];
            $target_dir = "../uploads/payment_proofs/";
            
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $payment_proof = "uploads/payment_proofs/" . $file_name;
            } else {
                echo "Failed to upload payment proof";
                exit;
            }
        }

        $sql = "INSERT INTO waste_donations (
            donor_name, 
            contact_number,
            email,
            food_type, 
            quantity, 
            location, 
            charges,
            payment_mode,
            payment_proof,
            created_at
        ) VALUES (
            '$donor_name', 
            '$contact_number',
            '$email',
            '$food_type', 
            '$quantity', 
            '$location',
            '$charges',
            '$payment_mode',
            '$payment_proof',
            NOW()
        )";

        if ($conn->query($sql) === TRUE) {
            // Add debug logging
            error_log("Database insert successful");
            
            // Send confirmation email
            $emailResult = sendDonationConfirmation(
                $email,
                $donor_name,
                $food_type,
                $quantity
            );
            
            // Add more debug logging
            if ($emailResult) {
                error_log("Email sent successfully to: " . $email);
            } else {
                error_log("Failed to send email to: " . $email);
            }
            
            echo "success";
        } else {
            error_log("Database error: " . $conn->error);
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Missing required fields!";
    }
} else {
    echo "Invalid request!";
}
?> 