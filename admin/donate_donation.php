<?php
include '../config.php'; // Ensure correct path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donationId = $_POST['id'];
    
    // Start transaction
    $conn->begin_transaction();

    try {
        // 1. First get the donation details from donations table
        $fetch_sql = "SELECT * FROM donations WHERE id = ?";
        $stmt = $conn->prepare($fetch_sql);
        $stmt->bind_param("i", $donationId);
        $stmt->execute();
        $result = $stmt->get_result();
        $donation = $result->fetch_assoc();

        if (!$donation) {
            throw new Exception("Donation not found");
        }

        // 2. Update status in donations table to 'Donated'
        $update_sql = "UPDATE donations SET status = 'Donated' WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $donationId);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update donation status");
        }

        // 3. Insert into donated_donations table
        $insert_sql = "INSERT INTO donated_donations (donation_id, food_name, quantity) 
                      VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("isi", 
            $donationId, 
            $donation['food_name'], 
            $donation['quantity']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert into donated_donations");
        }

        // 4. Remove from claimed_donations table
        $delete_sql = "DELETE FROM claimed_donations WHERE donation_id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $donationId);
        $stmt->execute();

        // If all operations are successful, commit the transaction
        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Donation successfully marked as donated'
        ]);

    } catch (Exception $e) {
        // If any operation fails, rollback all changes
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