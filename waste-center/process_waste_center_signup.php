<?php
session_start();
include '../config.php'; // Ensure correct path to config file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $location = trim($_POST['location']);

    // Check if username already exists
    $check_sql = "SELECT id FROM waste_centers WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $_SESSION['error'] = "Username already taken. Choose a different one.";
        header("Location: waste_center_signup.php");
        exit();
    }
    $check_stmt->close();

    // Secure password hashing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handling file upload
    $proof = $_FILES['proof']['name'];
    $target_dir = "../uploads/"; // Uploads folder in the root directory
    $target_file = $target_dir . basename($proof);

    // Ensure uploads directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Restrict file types
    $allowed_types = ['pdf', 'png', 'jpg', 'jpeg'];
    $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_types)) {
        $_SESSION['error'] = "Only PDF, PNG, JPG, and JPEG files are allowed.";
        header("Location: waste_center_signup.php");
        exit();
    }

    // Check if proof filename already exists
    $check_proof_sql = "SELECT id FROM waste_centers WHERE proof = ?";
    $check_proof_stmt = $conn->prepare($check_proof_sql);
    $check_proof_stmt->bind_param("s", $proof);
    $check_proof_stmt->execute();
    $check_proof_stmt->store_result();

    if ($check_proof_stmt->num_rows > 0) {
        $_SESSION['error'] = "A waste center with this proof file already exists.";
        header("Location: waste_center_signup.php");
        exit();
    }
    $check_proof_stmt->close();

    // Move the uploaded file to the uploads folder
    if (move_uploaded_file($_FILES["proof"]["tmp_name"], $target_file)) {
        // Insert into database
        $sql = "INSERT INTO waste_centers (username, password, location, proof) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $hashed_password, $location, $proof);

        if ($stmt->execute()) {
            // Redirect to login page after successful registration
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: waste_center_login.php");
            exit();
        } else {
            $_SESSION['error'] = "Error registering. Try again.";
            header("Location: waste_center_signup.php");
            exit();
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Failed to upload proof.";
        header("Location: waste_center_signup.php");
        exit();
    }
}
?>