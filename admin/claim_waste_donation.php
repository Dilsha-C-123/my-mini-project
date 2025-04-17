<?php
include '../config.php';
require '../donor/send_email.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donation_id = $_POST['id'];
    
    // First, get the donation details
    $query = "SELECT * FROM waste_donations WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $donation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $donation = $result->fetch_assoc();

    if (!$donation) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Donation not found'
        ]);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update donation status
        $update_sql = "UPDATE waste_donations SET status = 'claimed', claim_date = NOW() WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $donation_id);
        
        if ($stmt->execute()) {
            // Send claim confirmation email
            if (isset($donation['email']) && !empty($donation['email'])) {
                try {
                    sendWasteClaimConfirmationEmail(
                        $donation['email'],
                        $donation['donor_name'],
                        $donation['food_type'],
                        $donation['quantity'],
                        $donation['charges']
                    );
                } catch (Exception $e) {
                    error_log("Email sending failed: " . $e->getMessage());
                    // Continue with the process even if email fails
                }
            }

            $conn->commit();
            echo json_encode([
                'status' => 'success',
                'message' => 'Waste donation claimed successfully'
            ]);
        } else {
            throw new Exception("Error updating donation status");
        }
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error in claim_waste_donation.php: " . $e->getMessage());
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