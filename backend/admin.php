<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $location = $_POST['location'];
    $frequency = $_POST['frequency'];
    $risks = $_POST['risks'];

    $sql = "INSERT INTO explosions (location, frequency, risks) VALUES ('$location', '$frequency', '$risks')";
    if ($conn->query($sql) === TRUE) {
        echo "Explosion details submitted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <form method="POST" action="">
        <label for="location">Location:</label>
        <input type="text" name="location" required><br><br>

        <label for="frequency">Frequency:</label>
        <input type="text" name="frequency" required><br><br>

        <label for="risks">Risks:</label>
        <textarea name="risks" required></textarea><br><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
