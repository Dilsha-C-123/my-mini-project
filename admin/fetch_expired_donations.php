<?php
include '../config.php'; // Ensure correct path

// Fetch expired donations with all columns
$expired_sql = "SELECT * FROM donations WHERE status = 'Expired'";
$expired_result = $conn->query($expired_sql);

if ($expired_result === false) {
    die("Error fetching expired donations: " . $conn->error);
}

// Generate HTML for the expired donations table
$html = '';
while ($row = $expired_result->fetch_assoc()) {
    $html .= '<tr id="expired-' . $row['id'] . '">';
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