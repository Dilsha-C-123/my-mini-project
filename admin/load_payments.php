<?php
include '../config.php';

// Fetch pending payments - Modified query
$pending_payments_sql = "
    SELECT 
        p.id as payment_id,
        wc.username as center_name,
        wc.location,
        p.amount,
        p.payment_date,
        p.food_id as expired_food_id
    FROM payments p
    JOIN waste_centers wc ON wc.username = p.waste_center_id
    WHERE p.payment_status = 'Pending'
    ORDER BY p.payment_date DESC";

$pending_result = $conn->query($pending_payments_sql);

// Fetch completed payments - Modified query
$completed_payments_sql = "
    SELECT 
        p.id as payment_id,
        wc.username as center_name,
        wc.location,
        p.amount,
        p.payment_date,
        p.payment_mode,
        p.food_id as expired_food_id,
        p.payment_proof
    FROM payments p
    JOIN waste_centers wc ON wc.username = p.waste_center_id
    WHERE p.payment_status = 'Completed'
    ORDER BY p.payment_date DESC";

$completed_result = $conn->query($completed_payments_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    <!-- Pending Payments Table -->
    <div class="container mt-5">
        <h3>Pending Payments</h3>
        <?php if ($pending_result && $pending_result->num_rows > 0) { ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Payment ID</th>
                            <th>Expired Food ID</th>
                            <th>Center Name</th>
                            <th>Location</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                            <th>Payment Mode</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $pending_result->fetch_assoc()) { ?>
                            <tr id="payment-<?php echo $row['payment_id']; ?>">
                                <td><?php echo $row['payment_id']; ?></td>
                                <td><?php echo $row['expired_food_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['center_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td>₹<?php echo htmlspecialchars($row['amount']); ?></td>
                                <td><?php echo date('d-m-Y H:i', strtotime($row['payment_date'])); ?></td>
                                <td>
                                    <span class="badge bg-info">Offline</span>
                                </td>
                                <td>
                                    <button class="btn btn-success btn-sm verify-payment" 
                                            data-payment-id="<?php echo $row['payment_id']; ?>">
                                        Paid
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p class="text-muted">No pending payments found.</p>
        <?php } ?>
    </div>

    <!-- Completed Payments Table -->
    <div class="container mt-5">
        <h3>Completed Payments</h3>
        <?php if ($completed_result && $completed_result->num_rows > 0) { ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Payment ID</th>
                            <th>Expired Food ID</th>
                            <th>Center Name</th>
                            <th>Location</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                            <th>Payment Mode</th>
                            <th>Payment Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $completed_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['payment_id']; ?></td>
                                <td><?php echo $row['expired_food_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['center_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td>₹<?php echo htmlspecialchars($row['amount']); ?></td>
                                <td><?php echo date('d-m-Y H:i', strtotime($row['payment_date'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $row['payment_mode'] == 'online' ? 'primary' : 'info'; ?>">
                                        <?php echo ucfirst(htmlspecialchars($row['payment_mode'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($row['payment_proof'])) { 
                                        // If the path already includes 'uploads/payment_proofs/', remove it to avoid duplication
                                        $display_proof = str_replace('uploads/payment_proofs/', '', $row['payment_proof']);
                                    ?>
                                        <button class="btn btn-info btn-sm view-proof" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#proofModal" 
                                                data-proof="../uploads/payment_proofs/<?php echo htmlspecialchars($display_proof); ?>">
                                            View Proof
                                        </button>
                                    <?php } else { ?>
                                        <span class="text-muted">No proof</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p class="text-muted">No completed payments found.</p>
        <?php } ?>
    </div>

    <!-- Payment Proof Modal -->
    <div class="modal fade" id="proofModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Proof</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="proofImage" class="img-fluid" alt="Payment Proof">
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Handle viewing payment proof
            $('.view-proof').click(function() {
                var proofUrl = $(this).data('proof');
                
                // Clear previous error handlers and errors
                $('#proofImage').off('error load');
                $('.image-error').remove();
                
                // Set new image source
                $('#proofImage').attr('src', proofUrl)
                    .on('error', function() {
                        $(this).hide();
                        $('.image-error').remove(); // Remove any existing error messages
                        $(this).after('<div class="alert alert-danger image-error">Unable to load image. Please check if the file exists at: ' + proofUrl + '</div>');
                    })
                    .on('load', function() {
                        $(this).show();
                        $('.image-error').remove(); // Remove error messages if image loads successfully
                    });
            });

            // Handle verifying payments
            $('.verify-payment').click(function() {
                var button = $(this);
                var paymentId = button.data('payment-id');
                
                if (confirm('Are you sure you want to verify this payment?')) {
                    $.ajax({
                        url: 'verify_payment.php',
                        type: 'POST',
                        data: { payment_id: paymentId },
                        success: function(response) {
                            try {
                                var data = JSON.parse(response);
                                if (data.status === 'success') {
                                    $('#payment-' + paymentId).remove();
                                    location.reload();
                                } else {
                                    alert('Error: ' + data.message);
                                }
                            } catch (e) {
                                alert('Error processing response');
                            }
                        },
                        error: function() {
                            alert('Error processing request');
                        }
                    });
                }
            });
        });
    </script>

</body>
</html>