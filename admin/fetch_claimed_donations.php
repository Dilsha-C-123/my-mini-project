<?php
include '../config.php'; // Ensure correct path

// Fetch claimed donations with all columns
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

if ($claimed_result === false) {
    die("Error fetching claimed donations: " . $conn->error);
}

// Generate HTML for the claimed donations table
$html = '';
while ($row = $claimed_result->fetch_assoc()) {
    $html .= '<tr id="claimed-' . $row['claim_id'] . '">';
    $html .= '<td>' . $row['donation_id'] . '</td>';
    $html .= '<td>' . htmlspecialchars($row['donor_name']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['contact_number']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['food_name']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['quantity']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['expiry_date']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['location']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['status']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['created_at']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['food_type']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['claim_date']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['claimed_by']) . '</td>';
    $html .= '<td><button class="btn btn-primary donate-btn" data-id="' . $row['donation_id'] . '">Donate</button></td>';
    $html .= '</tr>';
}

echo $html;
?>