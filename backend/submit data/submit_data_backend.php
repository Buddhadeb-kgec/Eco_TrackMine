<?php
// Include the database connection
require_once '../config/database.php';

// Check if form data is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Group 1: Direct Emissions
    $coal_extraction = $_POST['coal_extraction'];
    $coal_type = $_POST['coal_type'];
    $methane_leakage = $_POST['methane_leakage'];

    // Group 2: Energy Consumption
    $electricity = $_POST['electricity'];
    $diesel = $_POST['diesel'];
    $petrol = $_POST['petrol'];

    // Group 3: Indirect Emissions
    $waste_coal = $_POST['waste_coal'];
    $mine_depth = $_POST['mine_depth'];
    $area_factors = $_POST['area_factors'];
    $explosions = $_POST['explosions'];

    // Sustainability Initiatives
    $sustainability_initiatives = $_POST['sustainability_initiatives'];

    // Calculate total emissions
    $emission_factor = 2.5; // Example emission factor
    $direct_emissions = $coal_extraction * $emission_factor + $methane_leakage;
    $energy_emissions = ($electricity * 0.5) + ($diesel * 2.7) + ($petrol * 2.3); // Example conversion factors
    $indirect_emissions = $waste_coal * $area_factors + $mine_depth * $explosions;

    $total_emissions = $direct_emissions + $energy_emissions + $indirect_emissions;

    // Insert data into mining_data table
    $query = "INSERT INTO mining_data 
        (coal_extraction, coal_type, methane_leakage, electricity, diesel, petrol, waste_coal, mine_depth, area_factors, explosions, direct_emissions, energy_emissions, indirect_emissions, total_emissions) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "ssdddddddddddd",
        $coal_extraction,
        $coal_type,
        $methane_leakage,
        $electricity,
        $diesel,
        $petrol,
        $waste_coal,
        $mine_depth,
        $area_factors,
        $explosions,
        $direct_emissions,
        $energy_emissions,
        $indirect_emissions,
        $total_emissions
    );
    $stmt->execute();

    // Insert sustainability initiatives if provided
    if (!empty($sustainability_initiatives)) {
        $query2 = "INSERT INTO sustainability_initiatives (initiative) VALUES (?)";
        $stmt2 = $conn->prepare($query2);
        $stmt2->bind_param("s", $sustainability_initiatives);
        $stmt2->execute();
        $stmt2->close();
    }

    // Close connection and redirect to success page
    $stmt->close();
    $conn->close();

    header("Location: ../front-end/success.html");
    exit();
} else {
    header("Location: ../front-end/submit_data.html?error=invalid_request");
    exit();
}
?>
