<?php
include '../config.php'; // Changed to go up one directory to root
require 'send_email.php';

function isValidEmail($email) {
    // Common email domains
    $commonDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com'];
    
    // Educational institution domain endings
    $eduDomains = ['.edu', '.edu.in', '.ac.in'];
    
    // Extract domain from email
    $domain = strtolower(substr(strrchr($email, "@"), 1));
    
    // Check if it's a common domain or educational domain
    $isCommonDomain = in_array($domain, $commonDomains);
    $isEduDomain = false;
    foreach ($eduDomains as $eduDomain) {
        if (str_ends_with($domain, $eduDomain)) {
            $isEduDomain = true;
            break;
        }
    }
    
    return filter_var($email, FILTER_VALIDATE_EMAIL) && ($isCommonDomain || $isEduDomain);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and validate email
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (!isValidEmail($email)) {
        echo "Invalid email address";
        exit;
    }

    // Remove the direct database connection since it's in config.php
    if (isset($_POST['donor_name'], $_POST['contact_number'], $_POST['food_name'], $_POST['food_type'], $_POST['quantity'], $_POST['expiry_date'], $_POST['location'])) {

        // Validate phone number
        $contact_number = preg_replace('/[^0-9]/', '', $_POST['contact_number']);
        if (!preg_match("/^[6-9][0-9]{9}$/", $contact_number)) {
            echo "Invalid phone number format";
            exit;
        }

        $donor_name = $conn->real_escape_string($_POST['donor_name']);
        $food_name = $conn->real_escape_string($_POST['food_name']);
        $food_type = $conn->real_escape_string($_POST['food_type']);
        $quantity = $conn->real_escape_string($_POST['quantity']);
        $expiry_date = $_POST['expiry_date'];
        $location = $conn->real_escape_string($_POST['location']);
        
        // Convert DD/MM/YYYY to YYYY-MM-DD for MySQL
        $date_parts = explode('/', $expiry_date);
        if (count($date_parts) === 3) {
            $formatted_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
        } else {
            echo "Invalid date format";
            exit;
        }

        // Validate date
        $input_date = DateTime::createFromFormat('Y-m-d', $formatted_date);
        $today = new DateTime();
        if ($input_date < $today) {
            echo "Please enter a future date";
            exit;
        }

        // Add this validation after getting the quantity
        if ($quantity < 3) {
            echo "Minimum donation quantity is 3 kg";
            exit;
        }

        // Calculate charges for waste food
        $charges = 0;
        if ($food_type === 'Waste Food') {
            $charges = $quantity * 10; // ₹10 per kg
            
            // Handle payment proof upload
            if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['payment_proof'];
                $file_name = time() . '_' . $file['name'];
                $target_dir = "../uploads/payment_proofs/";
                
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $target_file = $target_dir . $file_name;
                move_uploaded_file($file['tmp_name'], $target_file);
                $payment_proof = "uploads/payment_proofs/" . $file_name;
            } else {
                echo "Payment proof required for waste food";
                exit;
            }
        }

        // Only proceed if email is valid
        $sql = "INSERT INTO donations (
            donor_name,
            email, 
            contact_number, 
            food_name, 
            food_type, 
            quantity, 
            expiry_date, 
            location, 
            charges,
            payment_proof
        ) VALUES (
            '$donor_name',
            '$email', 
            '$contact_number', 
            '$food_name', 
            '$food_type', 
            '$quantity', 
            '$formatted_date', 
            '$location',
            '$charges',
            " . ($food_type === 'Waste Food' ? "'$payment_proof'" : "NULL") . "
        )";

        if ($conn->query($sql) === TRUE) {
            // Send confirmation email silently
            sendDonationConfirmation(
                $_POST['email'],
                $_POST['donor_name'],
                $_POST['food_name'],
                $_POST['quantity']
            );
            
            // Just return success, regardless of email status
            echo "success";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Missing form fields!";
    }
} else {
    echo "Invalid request!";
}
?>
