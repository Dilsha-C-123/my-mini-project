<?php
session_start();
include '../config.php';

if (!isset($_SESSION['waste_center'])) {
    header("Location: waste_center_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donation_id = $_POST['donation_id'];
    $amount = $_POST['amount'];
    $payment_mode = $_POST['payment_mode'];
    $waste_center = $_SESSION['waste_center'];
    $payment_status = ($payment_mode === 'offline') ? 'Pending' : 'Pending';
    
    // Start transactionthe 
    $conn->begin_transaction();
    
    try {
        // Insert payment record
        $payment_sql = "INSERT INTO payments (waste_center_id, amount, payment_mode, payment_status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($payment_sql);
        $stmt->bind_param("idss", $_SESSION['waste_center_id'], $amount, $payment_mode, $payment_status);
        $stmt->execute();
        $payment_id = $conn->insert_id;

        // Handle payment proof upload for online payments
        if ($payment_mode === 'online' && isset($_FILES['payment_proof'])) {
            $file = $_FILES['payment_proof'];
            $file_name = time() . '_' . $file['name'];
            $target_dir = "../uploads/payment_proofs/";
            
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                // Update payment record with proof
                $update_sql = "UPDATE payments SET payment_proof = ? WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("si", $file_name, $payment_id);
                $stmt->execute();
            }
        }

        // Insert claim record
        $claim_sql = "INSERT INTO claims (donation_id, waste_center, payment_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($claim_sql);
        $stmt->bind_param("isi", $donation_id, $waste_center, $payment_id);
        $stmt->execute();

        $conn->commit();
        
        // Redirect back to dashboard with success message
        header("Location: waste_center_dashboard.php?success=1");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: waste_payment.php?id=" . $donation_id . "&error=1");
        exit();
    }
}

// Get donation ID from URL
$donation_id = $_GET['id'];

// Fetch donation details
$sql = "SELECT 
    wd.id,
    wd.food_name,
    wd.quantity,
    wd.created_at as expiry_date,
    wd.location,
    wd.food_type,
    wd.charges
FROM waste_donations wd
WHERE wd.id = ? AND wd.status = 'claimed'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donation_id);
$stmt->execute();
$result = $stmt->get_result();
$donation = $result->fetch_assoc();

// Use the charges from the waste_donations table instead of calculating
$total_cost = $donation['charges'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Payment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .payment-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .upi-details {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="waste_center_dashboard.php">Waste Center Dashboard</a>
            <div class="ms-auto">
                <span class="navbar-text text-light">Welcome, <?php echo htmlspecialchars($_SESSION['waste_center']); ?></span>
                <a href="logout.php" class="btn btn-danger ms-3">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                There was an error processing your payment. Please try again.
            </div>
        <?php endif; ?>

        <h2 class="mb-4">Waste Collection Payment</h2>

        <!-- Donation Details -->
        <div class="payment-info">
            <h4>Donation Details</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Food ID:</strong> <?php echo htmlspecialchars($donation['id']); ?></p>
                    <p><strong>Food Type:</strong> <?php echo htmlspecialchars($donation['food_type']); ?></p>
                    <p><strong>Quantity:</strong> <?php echo htmlspecialchars($donation['quantity']); ?> kg</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($donation['location']); ?></p>
                    <p><strong>Expiry Date:</strong> <?php echo date('d-m-Y', strtotime($donation['expiry_date'])); ?></p>
                    <p><strong>Total Cost:</strong> ₹<?php echo number_format($total_cost, 2); ?></p>
                </div>
            </div>
        </div>

        <!-- UPI Payment Details -->
        <div class="upi-details">
            <h4>UPI Payment Details</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>UPI ID:</strong> dilzchenath@oksbi</p>
                    <p><strong>Amount:</strong> ₹<?php echo number_format($total_cost, 2); ?></p>
                    <p class="text-muted">Please make the payment using any UPI app and upload the screenshot below.</p>
                </div>
                <div class="col-md-6 text-center">
                    <img src="../uploads/upi-qr.jpg" alt="UPI QR Code" style="max-width: 200px; height: auto;">
                    <p class="mt-2">Scan QR code to pay</p>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="card mb-5">
            <div class="card-body">
                <h4 class="card-title mb-4">Payment Submission</h4>
                <form id="paymentForm" action="process_waste_payment.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="donation_id" value="<?php echo $donation_id; ?>">
                    <input type="hidden" name="amount" value="<?php echo $total_cost; ?>">

                    <div class="mb-3">
                        <label class="form-label">Payment Mode</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_mode" value="online" id="online" checked>
                            <label class="form-check-label" for="online">Online Payment (UPI)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_mode" value="offline" id="offline">
                            <label class="form-check-label" for="offline">Offline Payment</label>
                        </div>
                    </div>

                    <div id="onlinePaymentSection">
                        <div class="mb-3">
                            <label class="form-label">Upload Payment Screenshot</label>
                            <input type="file" class="form-control" name="payment_proof" accept="image/*">
                            <div class="form-text">Please upload the screenshot of your UPI payment.</div>
                        </div>
                    </div>

                    <div id="offlinePaymentSection" style="display: none;">
                        <div class="alert alert-info">
                            Please visit our office to make the payment. Your claim will be processed after payment verification.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="waste_center_dashboard.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('input[name="payment_mode"]').change(function() {
                if ($(this).val() === 'online') {
                    $('#onlinePaymentSection').show();
                    $('#offlinePaymentSection').hide();
                } else {
                    $('#onlinePaymentSection').hide();
                    $('#offlinePaymentSection').show();
                }
            });

            $('#paymentForm').submit(function(e) {
                const paymentMode = $('input[name="payment_mode"]:checked').val();
                if (paymentMode === 'online') {
                    const paymentProof = $('input[name="payment_proof"]').val();
                    if (!paymentProof) {
                        e.preventDefault();
                        alert('Please upload payment proof for online payment');
                        return false;
                    }
                }
            });
        });
    </script>
</body>
</html>
