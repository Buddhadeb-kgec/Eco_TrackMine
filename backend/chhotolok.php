<?php
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$fuel = $_POST['fuel'];
$petrol = $_POST['petrol'];
$diesel = $_POST['diesel'];
$electricity = $_POST['electricity'];

$sql = "INSERT INTO consumption_data (fuel, petrol, diesel, electricity)
VALUES ('$fuel', '$petrol', '$diesel', '$electricity')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
