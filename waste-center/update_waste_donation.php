<?php
include '../config.php';
session_start();

if (!isset($_SESSION['waste_center'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donationId = $_POST['id'];
    $status = $_POST['status'];
    $currentDateTime = date('Y-m-d H:i:s');
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update the waste donation status
        $update_sql = "UPDATE waste_donations 
                      SET status = ?,
                          updated_at = ?
                      WHERE id = ? AND status = 'claimed'";
        
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssi", $status, $currentDateTime, $donationId);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $conn->commit();
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Donation status updated successfully'
                ]);
            } else {
                throw new Exception("No rows were updated");
            }
        } else {
            throw new Exception("Failed to update donation status");
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