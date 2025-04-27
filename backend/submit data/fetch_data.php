<?php
// Include database connection
require_once 'db_connection.php';

// Fetch mining data with company details
$query = "SELECT cd.company_name, md.mining_type, md.mined_area, md.total_emissions 
          FROM company_details cd 
          JOIN mining_data md ON cd.id = md.company_id";
$result = $conn->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Return data as JSON
echo json_encode($data);
$conn->close();
?>
