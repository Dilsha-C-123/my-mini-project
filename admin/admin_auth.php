<?php
session_start();
include("../config.php");
 // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT password FROM admin_table WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_password);
        $stmt->fetch();

        // Verify password
        if ($password === $db_password) { // Use password_verify($password, $db_password) if hashed
            $_SESSION["admin_logged_in"] = true;
            $_SESSION["admin_email"] = $email;

            $stmt->close();
            $conn->close();

            header("Location: admin_dashboard.php"); // Redirect to dashboard
            exit();
        }
    }

    // If credentials are wrong
    $stmt->close();
    $conn->close();
    echo "<script>alert('Invalid credentials!'); window.location.href='admin_login.html';</script>";
}
?>
