<?php
include '../config.php'; // Ensure correct path

// Fetch donated donations with all columns
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
    GROUP BY d.id"; // Ensure unique records
$donated_result = $conn->query($donated_sql);

if ($donated_result === false) {
    die("Error fetching donated donations: " . $conn->error);
}

// Generate HTML for the donated donations table
$html = '';
while ($row = $donated_result->fetch_assoc()) {
    $html .= '<tr id="donated-' . $row['id'] . '">';
    $html .= '<td>' . $row['id'] . '</td>';
    $html .= '<td>' . htmlspecialchars($row['donor_name']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['contact_number']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['food_name']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['quantity']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['expiry_date']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['location']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['status']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['created_at']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['food_type']) . '</td>';
    $html .= '</tr>';
}

echo $html;
?>