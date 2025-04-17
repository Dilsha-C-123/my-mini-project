<?php
include '../config.php'; // Ensure correct path to config file

// Fetch all waste centers
$waste_centers_sql = "SELECT id, username, password, location, proof FROM waste_centers";
$waste_centers_result = $conn->query($waste_centers_sql);

if ($waste_centers_result === false) {
    die("Error fetching waste centers: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Waste Centers</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    <!-- Waste Centers Table -->
    <div class="container mt-5">
        <h3>Manage Waste Centers</h3>
        <?php if ($waste_centers_result->num_rows > 0) { ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Location</th>
                            <th>Proof</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="waste-centers">
                        <?php while ($row = $waste_centers_result->fetch_assoc()) { ?>
                            <tr id="waste-center-<?php echo $row['id']; ?>">
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td>
                                    <?php if (!empty($row['proof'])) { 
                                        // Remove timestamp prefix if it exists (everything before and including the first underscore)
                                        $display_proof = preg_replace('/^\d+_/', '', $row['proof']);
                                    ?>
                                        <button class="btn btn-info btn-sm view-proof" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#proofModal" 
                                                data-proof="../uploads/<?php echo htmlspecialchars($display_proof); ?>">
                                            View Proof
                                        </button>
                                    <?php } else { ?>
                                        <span class="text-muted">No proof uploaded</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm remove-btn" data-id="<?php echo $row['id']; ?>">Remove</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p class="text-muted">No waste centers registered.</p>
        <?php } ?>
    </div>

    <!-- Proof Modal -->
    <div class="modal fade" id="proofModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Waste Center Proof</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="proofImage" class="img-fluid" alt="Waste Center Proof">
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Handle proof viewing
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
                        $('.image-error').remove(); // Remove any error messages if image loads successfully
                    });
            });

            // Handle Remove Button Click
            $(document).on('click', '.remove-btn', function () {
                var wasteCenterId = $(this).data('id');
                if (confirm("Are you sure you want to remove this waste center?")) {
                    $.ajax({
                        url: 'remove_waste_center.php',
                        type: 'POST',
                        data: { id: wasteCenterId },
                        success: function (response) {
                            $('#waste-center-' + wasteCenterId).remove();
                        },
                        error: function (xhr, status, error) {
                            console.error("AJAX Error:", error);
                        }
                    });
                }
            });
        });
    </script>

</body>
</html>