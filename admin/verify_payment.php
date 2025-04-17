<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id'])) {
    $payment_id = $_POST['payment_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update payment status to Completed
        $update_sql = "UPDATE payments SET 
            payment_status = 'Completed',
            payment_mode = 'offline'
            WHERE id = ?";
            
        $stmt = $conn->prepare($update_sql);
        if (!$stmt) {
            throw new Exception("Error preparing update: " . $conn->error);
        }
        
        $stmt->bind_param("i", $payment_id);
        if (!$stmt->execute()) {
            throw new Exception("Error executing update: " . $stmt->error);
        }

        $conn->commit();
        
        echo json_encode(['status' => 'success']);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request'
    ]);
}
?> 