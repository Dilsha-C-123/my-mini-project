<?php
include '../config.php';
require '../donor/send_email.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donationId = $_POST['id'];
    $proofFile = $_POST['proof_file'];

    // First, get the donor's email before deleting the record
    $query = "SELECT email, donor_name FROM waste_donations WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $donationId);
    $stmt->execute();
    $result = $stmt->get_result();
    $donor = $result->fetch_assoc();

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete the record from waste_donations table
        $delete_sql = "DELETE FROM waste_donations WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $donationId);
        
        if ($stmt->execute()) {
            // If there's a proof file, delete it
            if ($proofFile) {
                $filePath = "../uploads/payment_proofs/" . basename($proofFile);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Send rejection email using the new function
            if ($donor && $donor['email']) {
                sendRejectionEmail($donor['email'], $donor['donor_name']);
            }

            // Commit transaction
            $conn->commit();

            echo json_encode([
                'status' => 'success',
                'message' => 'Donation rejected and deleted successfully'
            ]);
        } else {
            throw new Exception("Error deleting donation");
        }
    } catch (Exception $e) {
        // Rollback transaction on error
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