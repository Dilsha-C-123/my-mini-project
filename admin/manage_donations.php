<?php
include '../config.php'; // Ensure correct path

// Function to fetch donations by status
function fetchDonations($conn, $status) {
    $sql = "SELECT * FROM donations WHERE status = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $status);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetch pending donations
$pending_result = fetchDonations($conn, 'Pending');

// Fetch claimed donations
$claimed_sql = "
    SELECT 
        cd.id AS claim_id,
        d.id AS donation_id,
        d.donor_name, 
        d.contact_number, 
        d.food_name, 
        d.quantity, 
        cd.expiry_date, 
        d.location, 
        d.status, 
        d.created_at, 
        d.food_type,
        cd.claim_date,
        cd.claimed_by
    FROM claimed_donations cd
    JOIN donations d ON cd.donation_id = d.id
";
$claimed_result = $conn->query($claimed_sql);

// Fetch expired donations
$expired_result = fetchDonations($conn, 'Expired');

// Fetch donated donations
$donated_sql = "
    SELECT 
        d.id, 
        d.donor_name, 
        d.contact_number, 
        d.food_name, 
        d.quantity, 
        d.expiry_date, 
        d.location, 
        d.status, 
        d.created_at, 
        d.food_type 
    FROM donated_donations dd
    JOIN donations d ON dd.donation_id = d.id
";
$donated_result = $conn->query($donated_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Donations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .main-buttons {
            margin-bottom: 20px;
        }
        .main-buttons .btn {
            margin-right: 15px;
            padding: 15px 40px;
            font-size: 1.2rem;
            font-weight: 500;
        }
        .sub-tabs {
            margin-bottom: 20px;
        }
        .sub-tabs .nav-link {
            color: #666;
            font-weight: 500;
            padding: 12px 30px;
            font-size: 1.1rem;
        }
        .sub-tabs .nav-link.active {
            color: #28a745;
            font-weight: 600;
        }
        .table-container {
            padding: 20px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .active-section {
            display: block;
        }
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
        }
        .nav-tabs .nav-item {
            margin-bottom: -2px;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #495057;
            padding: 12px 30px;
            font-size: 16px;
            cursor: pointer;
            background: transparent;
        }
        .nav-tabs .nav-link:hover {
            border: none;
            border-bottom: 2px solid #dee2e6;
        }
        .nav-tabs .nav-link.active {
            color: #0d6efd;
            background-color: transparent;
            border: none;
            border-bottom: 2px solid #0d6efd;
            font-weight: 500;
        }
        /* New modern tab styling */
        .custom-tabs {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .tab-button {
            padding: 12px 25px;
            font-size: 15px;
            border: none;
            background-color: #f0f0f0;
            color: #666;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .tab-button:hover {
            background-color: #e0e0e0;
            color: #333;
        }
        .tab-button.active {
            background-color: #4CAF50;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        #proofImage {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .modal-body {
            padding: 20px;
        }
        .modal-dialog {
            max-width: 800px; /* or your preferred width */
        }
        .navbar-nav .nav-link {
            color: rgba(255,255,255,.8) !important;
            padding: 0.5rem 1rem;
            transition: color 0.3s ease;
        }
        .navbar-nav .nav-link:hover {
            color: #fff !important;
        }
        .navbar-nav .nav-link.active {
            color: #fff !important;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container-fluid mt-4">
    <!-- Main buttons -->
    <div class="main-buttons mb-4">
        <button class="btn btn-success me-2" onclick="showSection('pending')">Pending Donations</button>
        <button class="btn btn-primary me-2" onclick="showSection('claimed')">Claimed Donations</button>
        <button class="btn btn-primary" onclick="showSection('donated')">Donated Donations</button>
    </div>

    <!-- Pending Donations Section -->
    <div id="pending-section" class="donation-section">
        <!-- Sub-tabs for Pending -->
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link active" onclick="showSubTable('pending-fresh')">Fresh Food</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" onclick="showSubTable('pending-expired')">Expired Food</a>
            </li>
        </ul>
        
        <!-- Pending Fresh Table -->
        <div id="pending-fresh-table" class="sub-table">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donor Name</th>
                        <th>Food Name</th>
                        <th>Quantity</th>
                        <th>Expiry Date</th>
                        <th>Location</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM donations WHERE status='pending' ORDER BY id DESC";
                    $result = mysqli_query($conn, $query);
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>".$row['id']."</td>";
                        echo "<td>".$row['donor_name']."</td>";
                        echo "<td>".$row['food_name']."</td>";
                        echo "<td>".$row['quantity']."</td>";
                        echo "<td>".$row['expiry_date']."</td>";
                        echo "<td>".$row['location']."</td>";
                        echo "<td>".$row['contact_number']."</td>";
                        echo "<td>".$row['email']."</td>";
                        echo "<td>
                                <button class='btn btn-success btn-sm' onclick='acceptFreshDonation(".$row['id'].")'>Claim</button>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Pending Expired Table -->
        <div id="pending-expired-table" class="sub-table" style="display: none;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donor Name</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Food Type</th>
                        <th>Food Name</th>
                        <th>Quantity (kg)</th>
                        <th>Location</th>
                        <th>Charges</th>
                        <th>Payment Mode</th>
                        <th>Payment Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM waste_donations WHERE status='pending' ORDER BY id DESC";
                    $result = mysqli_query($conn, $query);
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>".$row['id']."</td>";
                        echo "<td>".$row['donor_name']."</td>";
                        echo "<td>".$row['contact_number']."</td>";
                        echo "<td>".$row['email']."</td>";
                        echo "<td>".$row['food_type']."</td>";
                        echo "<td>".$row['food_name']."</td>";
                        echo "<td>".$row['quantity']."</td>";
                        echo "<td>".$row['location']."</td>";
                        echo "<td>₹".$row['charges']."</td>";
                        echo "<td>".$row['payment_mode']."</td>";
                        echo "<td>";
                        if ($row['payment_proof']) {
                            $proofPath = "../uploads/payment_proofs/" . basename($row['payment_proof']);
                            echo "<button class='btn btn-info btn-sm' onclick='viewProof(\"" . $proofPath . "\")'>View Proof</button>";
                        } else {
                            echo "Pending";
                        }
                        echo "</td>";
                        echo "<td>
                                <div class='d-flex gap-2'>
                                    <button class='btn btn-success btn-sm' onclick='acceptWasteDonation(".$row['id'].")'>Claim</button>
                                    <button class='btn btn-danger btn-sm' onclick='rejectWasteDonation(".$row['id'].", \"".$row['payment_proof']."\")'>Reject</button>
                                </div>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Claimed Donations Section -->
    <div id="claimed-section" class="donation-section" style="display: none;">
        <!-- Sub-tabs for Claimed -->
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link active" onclick="showSubTable('claimed-fresh')">Fresh Food</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" onclick="showSubTable('claimed-expired')">Expired Food</a>
            </li>
        </ul>
        
        <!-- Claimed Fresh Food Table -->
        <div id="claimed-fresh-table" class="sub-table">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donor Name</th>
                        <th>Contact Number</th>
                        <th>Food Name</th>
                        <th>Quantity</th>
                        <th>Expiry Date</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Food Type</th>
                        <th>Claimed By</th>
                        <th>Claim Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Updated query to match your table structure
                    $query = "SELECT d.*, cd.claim_date, cd.claimed_by 
                             FROM donations d 
                             INNER JOIN claimed_donations cd ON d.id = cd.donation_id 
                             WHERE d.id NOT IN (SELECT donation_id FROM donated_donations)
                             ORDER BY cd.claim_date DESC";
                    
                    $result = mysqli_query($conn, $query);
                    
                    // Debug: Print the number of rows found
                    echo "<!-- Found " . mysqli_num_rows($result) . " rows -->";
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>".$row['id']."</td>";
                            echo "<td>".$row['donor_name']."</td>";
                            echo "<td>".$row['contact_number']."</td>";
                            echo "<td>".$row['food_name']."</td>";
                            echo "<td>".$row['quantity']."</td>";
                            echo "<td>".$row['expiry_date']."</td>";
                            echo "<td>".$row['location']."</td>";
                            echo "<td>".$row['status']."</td>";
                            echo "<td>".$row['created_at']."</td>";
                            echo "<td>".$row['food_type']."</td>";
                            echo "<td>".$row['claimed_by']."</td>";
                            echo "<td>".date('d-m-Y H:i', strtotime($row['claim_date']))."</td>";
                            echo "<td>
                                    <button class='btn btn-primary btn-sm' onclick='donateFreshFood(".$row['id'].")'>Donate</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='13' class='text-center'>No claimed fresh donations found</td></tr>";
                        // Debug information
                        if (!$result) {
                            echo "<!-- Query error: " . mysqli_error($conn) . " -->";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Add this debug section temporarily -->
        <div style="display: none;">
            <?php
            // Debug: Print the actual query
            echo "Query: " . $query . "<br>";
            
            // Debug: Print all claimed_donations records
            $debug_query = "SELECT * FROM claimed_donations";
            $debug_result = mysqli_query($conn, $debug_query);
            echo "Total claimed_donations: " . mysqli_num_rows($debug_result) . "<br>";
            
            // Debug: Print all donations records for the claimed IDs
            $claimed_ids = [];
            while($row = mysqli_fetch_assoc($debug_result)) {
                $claimed_ids[] = $row['donation_id'];
            }
            if (!empty($claimed_ids)) {
                $debug_query2 = "SELECT * FROM donations WHERE id IN (" . implode(',', $claimed_ids) . ")";
                $debug_result2 = mysqli_query($conn, $debug_query2);
                echo "Found matching donations: " . mysqli_num_rows($debug_result2);
            }
            ?>
        </div>

        <!-- Claimed Expired Food Table -->
        <div id="claimed-expired-table" class="sub-table" style="display: none;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donor Name</th>
                        <th>Contact Number</th>
                        <th>Food Type</th>
                        <th>Food Name</th>
                        <th>Quantity</th>
                        <th>Location</th>
                        <th>Charges</th>
                        <th>Payment Mode</th>
                        <th>Payment Proof</th>
                        <th>Claim Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Debug: Print the query
                    $query = "SELECT * FROM waste_donations WHERE status = 'claimed' ORDER BY id DESC";
                    $result = mysqli_query($conn, $query);
                    
                    // Debug: Print the number of rows
                    echo "<!-- Found " . mysqli_num_rows($result) . " rows -->";
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>".$row['id']."</td>";
                            echo "<td>".$row['donor_name']."</td>";
                            echo "<td>".$row['contact_number']."</td>";
                            echo "<td>".$row['food_type']."</td>";
                            echo "<td>".$row['food_name']."</td>";
                            echo "<td>".$row['quantity']."</td>";
                            echo "<td>".$row['location']."</td>";
                            echo "<td>₹".$row['charges']."</td>";
                            echo "<td>".$row['payment_mode']."</td>";
                            echo "<td>";
                            if ($row['payment_proof']) {
                                echo "<button class='btn btn-info btn-sm' onclick='viewProof(\"../uploads/payment_proofs/".basename($row['payment_proof'])."\")'>View Proof</button>";
                            } else {
                                echo "No Proof";
                            }
                            echo "</td>";
                            echo "<td>".date('d-m-Y H:i', strtotime($row['claim_date']))."</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='11' class='text-center'>No claimed expired donations found (".mysqli_error($conn).")</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        </div>

    <!-- Donated Donations Section -->
    <div id="donated-section" class="donation-section" style="display: none;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Donation ID</th>
                    <th>Food Name</th>
                    <th>Quantity</th>
                    <th>Donation Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query to get donated donations
                $query = "SELECT * FROM donated_donations ORDER BY donation_date DESC";
                $result = mysqli_query($conn, $query);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>".$row['id']."</td>";
                        echo "<td>".$row['donation_id']."</td>";
                        echo "<td>".$row['food_name']."</td>";
                        echo "<td>".$row['quantity']."</td>";
                        echo "<td>".date('d-m-Y H:i', strtotime($row['donation_date']))."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No donated donations found</td></tr>";
                    // Debug information
                    if (!$result) {
                        echo "<!-- Query error: " . mysqli_error($conn) . " -->";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add this debug section temporarily -->
    <div style="display: none;">
        <?php
        // Debug: Print the actual query and counts
        $debug_query = "SELECT COUNT(*) as count FROM donated_donations";
        $debug_result = mysqli_query($conn, $debug_query);
        $count = mysqli_fetch_assoc($debug_result)['count'];
        echo "<!-- Total donated donations: $count -->";
        
        // Check the last few donations
        $debug_query2 = "SELECT * FROM donated_donations ORDER BY id DESC LIMIT 5";
        $debug_result2 = mysqli_query($conn, $debug_query2);
        echo "<!-- Latest donations: ";
        while($row = mysqli_fetch_assoc($debug_result2)) {
            echo "ID: ".$row['id']." DonationID: ".$row['donation_id']." | ";
        }
        echo " -->";
        ?>
    </div>
</div>

<!-- Payment Proof Modal -->
<div class="modal fade" id="proofModal" tabindex="-1" aria-labelledby="proofModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proofModalLabel">Payment Proof</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="imageError" class="alert alert-danger" style="display: none;">
                    Error loading image. Please check the file path.
                </div>
                <img id="proofImage" src="" alt="Payment Proof" style="max-width: 100%; height: auto;"
                     onerror="document.getElementById('imageError').style.display='block'; this.style.display='none';"
                     onload="document.getElementById('imageError').style.display='none'; this.style.display='block';">
            </div>
            <div class="modal-footer">
                <div id="imagePath" style="font-size: 12px; color: #666; margin-right: auto;"></div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
// Add this at the start of your JavaScript section
document.addEventListener('DOMContentLoaded', function() {
    // Show pending section by default
    showSection('pending');
    
    // Add active class to the Manage Donations link in side navigation
    const sideNavLinks = document.querySelectorAll('.sidebar a');
    sideNavLinks.forEach(link => {
        if(link.getAttribute('href').includes('manage_donations.php')) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
});

function showSection(section) {
    // Hide all sections
    document.querySelectorAll('.donation-section').forEach(el => {
        el.style.display = 'none';
    });
    
    // Show selected section
    const sectionElement = document.getElementById(section + '-section');
    if (sectionElement) {
        sectionElement.style.display = 'block';
    }
    
    // Reset all main buttons to blue
    document.querySelectorAll('.main-buttons .btn').forEach(btn => {
        btn.classList.remove('btn-success');
        btn.classList.add('btn-primary');
    });
    
    // Make clicked button green
    const button = document.querySelector(`.main-buttons .btn[onclick="showSection('${section}')"]`);
    if(button) {
        button.classList.remove('btn-primary');
        button.classList.add('btn-success');
    }

    // Show appropriate sub-table based on section
    if (section === 'pending') {
        showSubTable('pending-fresh');
    } else if (section === 'claimed') {
        showSubTable('claimed-fresh');
    }
}

function showSubTable(tableId) {
    // Get the parent section
    const section = tableId.split('-')[0];
    
    // Hide all sub-tables in this section
    document.querySelectorAll(`#${section}-section .sub-table`).forEach(table => {
        table.style.display = 'none';
    });
    
    // Show selected table
    const selectedTable = document.getElementById(tableId + '-table');
    if (selectedTable) {
        selectedTable.style.display = 'block';
    }
    
    // Update active state of tabs
    document.querySelectorAll(`#${section}-section .nav-link`).forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Make clicked tab active
    event.target.classList.add('active');
}

function viewProof(imagePath) {
    // Set the image source
    const proofImage = document.getElementById('proofImage');
    proofImage.src = imagePath;
    
    // Show the modal
    const myModal = new bootstrap.Modal(document.getElementById('proofModal'));
    myModal.show();
}

// Add this function to test if the image exists
function testImagePath(imagePath) {
    fetch(imagePath)
        .then(response => {
            if (!response.ok) {
                console.error('Image not found:', imagePath);
                alert('Image not found at: ' + imagePath);
            }
        })
        .catch(error => {
            console.error('Error checking image:', error);
            alert('Error checking image: ' + error);
        });
}

function acceptFreshDonation(id) {
    if(confirm('Are you sure you want to claim this fresh food donation?')) {
                $.ajax({
                    url: 'claim_donation.php',
                    type: 'POST',
            data: { id: id },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if(result.status === 'success') {
                        alert('Donation claimed successfully!');
                        location.reload(); // Refresh to update tables
                        } else {
                        alert('Error: ' + result.message);
                    }
                } catch(e) {
                    alert('Error processing request');
                }
            },
            error: function() {
                alert('Error processing request');
            }
        });
    }
}

function acceptWasteDonation(id) {
    if(confirm('Are you sure you want to claim this expired food donation?')) {
        // Add console.log for debugging
        console.log('Claiming donation with ID:', id);

                $.ajax({
            url: 'claim_waste_donation.php',
                    type: 'POST',
            data: { id: id },
            dataType: 'json', // Specify expected data type
            success: function(response) {
                console.log('Response:', response); // Debug log
                if(response.status === 'success') {
                    alert('Donation claimed successfully!');
                    location.reload();
                        } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error); // Debug log
                console.error('Response Text:', xhr.responseText); // Debug log
                alert('Error processing request. Check console for details.');
            }
        });
    }
}

function rejectWasteDonation(id, proofFile) {
    if(confirm('Are you sure you want to reject this donation? This action cannot be undone.')) {
        $.ajax({
            url: 'reject_waste_donation.php',
            type: 'POST',
            data: { 
                id: id,
                proof_file: proofFile
            },
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    alert('Donation rejected successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Error processing request. Check console for details.');
            }
        });
    }
}

function donateFreshFood(id) {
    if(confirm('Are you sure you want to mark this food as donated?')) {
        $.ajax({
            url: 'donate_donation.php', // Using your existing file
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                console.log('Server response:', response); // Debug log
                if(response.status === 'success') {
                    alert('Food donated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                alert('Error processing request. Check console for details.');
            }
        });
    }
}
    </script>

</body>
</html>