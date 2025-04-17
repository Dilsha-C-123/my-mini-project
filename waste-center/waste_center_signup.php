<?php
session_start();

// Clear any existing session data for the registration process
unset($_SESSION['error']);
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Center Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-container {
            width: 500px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <div class="register-container">
        <h3 class="text-center">Waste Center Registration</h3>

        <!-- Display error messages -->
        <?php if (isset($_SESSION['error'])) { ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php } ?>

        <!-- Display success message and trigger modal -->
        <?php if (isset($_SESSION['success'])) { ?>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    setTimeout(function() {
                        window.location.href = "waste_center_login.php"; // Redirect after 2 seconds
                    }, 2000);
                });
            </script>
        <?php } ?>

        <!-- Registration Form -->
        <form action="process_waste_center_signup.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" name="location" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="proof" class="form-label">Upload Proof</label>
                <input type="file" name="proof" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Sign Up</button>
        </form>

        <p class="mt-3 text-center">Already have an account? <a href="./waste_center_login.php">Login</a></p>
    </div>

    <!-- Bootstrap Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Registration Successful</h5>
                </div>
                <div class="modal-body">
                    Your registration was successful! Redirecting to login page...
                </div>
            </div>
        </div>
    </div>

</body>
</html>