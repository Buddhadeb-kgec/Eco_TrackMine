<?php
// Include the database connection
include 'db_connection.php';

// Query data for visualization
$sql = "SELECT c.company_name, c.location, m.mining_type, m.mined_area, m.production_capacity, m.total_emissions, s.initiative
        FROM company_details c
        JOIN mining_data m ON c.id = m.company_id
        LEFT JOIN sustainability_initiatives s ON c.id = s.company_id";

$result = $conn->query($sql);

if (!$result) {
    die("Error fetching data: " . $conn->error); // Better error handling for debugging
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carbon Emission Visualization</title>
    <link rel="stylesheet" href="css/common.css"> <!-- Assuming you have a common.css file for styling -->
    <style>
        /* Add some basic table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h2>Carbon Emission Visualization</h2>

    <?php
    // Check if data is available
    if ($result->num_rows > 0) {
        echo '<table>
                <tr>
                    <th>Company Name</th>
                    <th>Location</th>
                    <th>Mining Type</th>
                    <th>Mined Area</th>
                    <th>Production Capacity (tons/year)</th>
                    <th>Total Emissions (kg CO2)</th>
                    <th>Sustainability Initiatives</th>
                </tr>';

        // Loop through and display each row of data
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['company_name']}</td>
                    <td>{$row['location']}</td>
                    <td>{$row['mining_type']}</td>
                    <td>{$row['mined_area']}</td>
                    <td>{$row['production_capacity']}</td>
                    <td>{$row['total_emissions']}</td>
                    <td>{$row['initiative']}</td>
                  </tr>";
        }

        echo '</table>';
    } else {
        echo "<p>No data available.</p>";
    }

    // Close the database connection
    $conn->close();
    ?>
    
    <!-- Optional: Add a link to go back or to other pages -->
    <div style="text-align: center; margin-top: 20px;">
        <a href="index.html">Go Back to Homepage</a>
    </div>
</body>
</html>
