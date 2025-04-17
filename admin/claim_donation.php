<?php
include '../config.php'; // Ensure correct path
require '../donor/send_email.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donation_id = $_POST['id'];
    
    // First, get the donation details
    $query = "SELECT * FROM donations WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $donation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $donation = $result->fetch_assoc();

    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update donation status
        $update_sql = "UPDATE donations SET status = 'claimed' WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $donation_id);
        
        if ($stmt->execute()) {
            // Insert into claimed_donations
            $claim_sql = "INSERT INTO claimed_donations (donation_id, claim_date, claimed_by) VALUES (?, NOW(), 'Admin')";
            $stmt = $conn->prepare($claim_sql);
            $stmt->bind_param("i", $donation_id);
            
            if ($stmt->execute()) {
                // Send claim confirmation email
                if ($donation && isset($donation['email'])) {
                    sendClaimConfirmationEmail(
                        $donation['email'],
                        $donation['donor_name'],
                        $donation['food_name'],
                        $donation['quantity']
                    );
                }

                $conn->commit();
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Donation claimed successfully'
                ]);
            } else {
                throw new Exception("Error recording claim");
            }
        } else {
            throw new Exception("Error updating donation status");
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>