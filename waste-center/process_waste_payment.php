<?php
session_start();
include '../config.php';

if (!isset($_SESSION['waste_center'])) {
    header("Location: waste_center_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donation_id = $_POST['donation_id'];
    $amount = $_POST['amount'];
    $payment_mode = $_POST['payment_mode'];
    $waste_center = $_SESSION['waste_center'];
    $payment_status = ($payment_mode === 'offline') ? 'Pending' : 'Completed';
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert payment record
        $payment_sql = "INSERT INTO payments (
            waste_center_id, 
            food_id,
            amount, 
            payment_mode, 
            payment_status, 
            payment_date
        ) VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($payment_sql);
        if (!$stmt) {
            throw new Exception("Error preparing payment query: " . $conn->error);
        }

        // Make sure we're using the waste center ID from the session
        $waste_center_id = $_SESSION['waste_center'];
        $stmt->bind_param("sidss", $waste_center_id, $donation_id, $amount, $payment_mode, $payment_status);
        $stmt->execute();
        $payment_id = $conn->insert_id;

        // Handle payment proof upload for both online and offline payments
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['payment_proof'];
            $file_name = time() . '_' . $file['name'];
            $target_dir = "../uploads/payment_proofs/";
            
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                // Store the complete path in database
                $proof_path = "uploads/payment_proofs/" . $file_name;
                $update_sql = "UPDATE payments SET payment_proof = ? WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                if (!$stmt) {
                    throw new Exception("Error updating payment proof: " . $conn->error);
                }
                $stmt->bind_param("si", $proof_path, $payment_id);
                $stmt->execute();
            } else {
                throw new Exception("Failed to upload file");
            }
        }

        // Add error logging
        error_log("Payment proof upload: " . print_r($_FILES, true));

        // Insert or update claim record with payment_id
        $claim_sql = "INSERT INTO claims (
            donation_id, 
            waste_center, 
            payment_id,
            claimed_at
        ) VALUES (?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE payment_id = ?";
        
        $stmt = $conn->prepare($claim_sql);
        if (!$stmt) {
            throw new Exception("Error preparing claim query: " . $conn->error);
        }
        $stmt->bind_param("isii", $donation_id, $waste_center, $payment_id, $payment_id);
        $stmt->execute();

        // Update donation status
        $update_donation_sql = "UPDATE waste_donations SET status = 'completed' WHERE id = ?";
        $stmt = $conn->prepare($update_donation_sql);
        if (!$stmt) {
            throw new Exception("Error updating donation status: " . $conn->error);
        }
        $stmt->bind_param("i", $donation_id);
        $stmt->execute();

        $conn->commit();
        header("Location: waste_center_dashboard.php?success=1");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Payment Processing Error: " . $e->getMessage());
        header("Location: waste_payment.php?id=" . $donation_id . "&error=1");
        exit();
    }
}
?>
