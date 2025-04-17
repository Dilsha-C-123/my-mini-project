<?php
session_start();
include '../config.php'; // Ensure correct path to config file

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $waste_center_id = intval($_POST['id']);

    // Fetch the proof filename before deleting the record
    $fetch_sql = "SELECT proof FROM waste_centers WHERE id = ?";
    $fetch_stmt = $conn->prepare($fetch_sql);
    $fetch_stmt->bind_param("i", $waste_center_id);
    $fetch_stmt->execute();
    $fetch_stmt->bind_result($proof_filename);
    $fetch_stmt->fetch();
    $fetch_stmt->close();

    // Delete the waste center record from the database
    $delete_sql = "DELETE FROM waste_centers WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $waste_center_id);

    if ($delete_stmt->execute()) {
        // Delete the proof file from the uploads folder
        if (!empty($proof_filename)) {
            $proof_path = "../uploads/" . $proof_filename;
            if (file_exists($proof_path)) {
                unlink($proof_path); // Delete the file
            }
        }
        echo "Waste center and proof file deleted successfully.";
    } else {
        echo "Error deleting waste center.";
    }
    $delete_stmt->close();
} else {
    echo "Invalid request.";
}
?>