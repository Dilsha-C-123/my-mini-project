<?php
include '../config.php'; // Ensure correct path

session_start();

// Add this temporarily at the top of the file after session_start()
error_log("Current Waste Center: " . print_r($_SESSION, true));

if (!isset($_SESSION['waste_center'])) {
    header("Location: waste_center_login.php");
    exit();
}

$waste_center = $_SESSION['waste_center'];

// Ensure database connection exists
if (!isset($conn)) {
    die("Database connection failed.");
}

// Add this debugging section at the top after your database connection
echo "<!-- Debugging Information -->";

// First, let's see all records in the waste_donations table
$debug_sql = "SELECT * FROM waste_donations";
$debug_result = $conn->query($debug_sql);
echo "<!-- Total records in waste_donations: " . $debug_result->num_rows . " -->";

// Add debugging information
echo "<!-- Starting database query -->";

// Update the query to show claimed donations in Available Expired Donations section
$expired_sql = "SELECT * FROM waste_donations WHERE status = 'claimed'";
$expired_result = $conn->query($expired_sql);

// Debug information
echo "<!-- Query: " . htmlspecialchars($expired_sql) . " -->";
echo "<!-- Number of results: " . ($expired_result ? $expired_result->num_rows : '0') . " -->";

// Let's see what's in each row
if ($expired_result && mysqli_num_rows($expired_result) > 0) {
    while($row = mysqli_fetch_assoc($expired_result)) {
        echo "<!-- Row ID: " . $row['id'] . ", Food Type: " . $row['food_type'] . ", Quantity: " . $row['quantity'] . " -->";
    }
}

// Let's also check what's in the table
$check_sql = "SELECT COUNT(*) as count, status FROM waste_donations GROUP BY status";
$check_result = $conn->query($check_sql);
while($row = $check_result->fetch_assoc()) {
    echo "<!-- Status: " . $row['status'] . " Count: " . $row['count'] . " -->";
}

// Add this before the claimed donations query
$debug_query = "SELECT * FROM payments WHERE waste_center_id = '" . $_SESSION['waste_center'] . "'";
$debug_result = $conn->query($debug_query);
error_log("Found payments: " . $debug_result->num_rows);

// Fetch claimed waste food
$claimed_sql = "
    SELECT 
        id, 
        donor_name,
        contact_number,
        food_type,
        food_name,
        quantity,
        location,
        charges,
        payment_mode,
        payment_proof,
        claim_date
    FROM waste_donations 
    WHERE status = 'claimed'";  // This will show all claimed expired donations

$result = $conn->query($claimed_sql);

// Debug information
echo "<!-- Number of claimed donations found: " . ($result ? $result->num_rows : '0') . " -->";

// Add debug information
echo "<!-- Debug Info:
Waste Center ID: " . htmlspecialchars($waste_center) . "
Expired Query: " . htmlspecialchars($expired_sql) . "
Claimed Query: " . htmlspecialchars($claimed_sql) . "
-->";

// Check if waste_center is set
if (!isset($waste_center) || empty($waste_center)) {
    die("Error: Waste center ID not set in session");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Center Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 40px;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .dashboard-header {
            background: #343a40;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .btn-claim {
            background-color: #28a745;
            color: white;
        }
        .btn-claim:hover {
            background-color: #218838;
        }
        .logout-btn {
            float: right;
            margin-right: 15px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Waste Center Dashboard</a>
            <div class="ms-auto">
                <span class="navbar-text text-light">Welcome, <?php echo htmlspecialchars($waste_center); ?></span>
                <a href="logout.php" class="btn btn-danger ms-3">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Available Expired Food Table -->
        <h3 class="text-center mt-4">Available Expired Donations</h3>
        <div id="pending-expired-table" class="sub-table">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Food Type</th>
                        <th>Quantity(kg)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Change this query to get claimed donations
                    $available_sql = "SELECT id, food_type, quantity FROM waste_donations WHERE status = 'claimed' ORDER BY claim_date DESC";
                    $available_result = $conn->query($available_sql);
                    
                    if ($available_result && mysqli_num_rows($available_result) > 0) {
                        while($row = mysqli_fetch_assoc($available_result)) {
                            echo "<tr>";
                            echo "<td>".$row['id']."</td>";
                            echo "<td>".htmlspecialchars($row['food_type'])."</td>";
                            echo "<td>".htmlspecialchars($row['quantity'])."</td>";
                            echo "<td>
                                    <button class='btn btn-success btn-sm' onclick='acceptWasteDonation(".$row['id'].")'>Claim</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>No available expired donations found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Claimed Expired Food Table -->
        <h3 class="text-center mt-5">Claimed Expired Donations</h3>
        <div id="claimed-expired-table" class="sub-table">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Food Type</th>
                        <th>Quantity(kg)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Add debug information
                    error_log("Waste Center ID: " . $_SESSION['waste_center']);

                    $claimed_sql = "SELECT wd.id, wd.food_type, wd.quantity 
                                  FROM waste_donations wd
                                  INNER JOIN payments p ON wd.id = p.food_id
                                  WHERE wd.status = 'completed' 
                                  AND p.waste_center_id = ?
                                  ORDER BY p.payment_date DESC";
            
                    $stmt = $conn->prepare($claimed_sql);
                    if (!$stmt) {
                        error_log("Prepare failed: " . $conn->error);
                    }
            
                    $stmt->bind_param("s", $_SESSION['waste_center']);
                    $stmt->execute();
                    $claimed_result = $stmt->get_result();
            
                    // Add debug information
                    error_log("Number of claimed donations found: " . $claimed_result->num_rows);
            
                    if ($claimed_result && mysqli_num_rows($claimed_result) > 0) {
                        while($row = mysqli_fetch_assoc($claimed_result)) {
                            echo "<tr>";
                            echo "<td>".$row['id']."</td>";
                            echo "<td>".htmlspecialchars($row['food_type'])."</td>";
                            echo "<td>".htmlspecialchars($row['quantity'])."</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>No completed expired donations found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Make sure both tables are visible by default
        $('#pending-expired-table').show();
        $('#claimed-expired-table').show();
    });

    function viewProof(imagePath) {
        window.open(imagePath, '_blank');
    }

    function acceptWasteDonation(id) {
        if(confirm('Are you sure you want to claim this expired food donation?')) {
            // Redirect to waste_payment.php with the donation ID
            window.location.href = 'waste_payment.php?id=' + id;
        }
    }
    </script>
</body>
</html>