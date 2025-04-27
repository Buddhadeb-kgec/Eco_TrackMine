<?php
include 'db.php';

$sql = "SELECT * FROM explosions ORDER BY timestamp DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explosion Details</title>
    <link rel="stylesheet" href="../front-end/css/common.css">
    <link rel="stylesheet" href="../front-end/css/explo.css">
</head>
<body>
    <h1>Methane Explosion Details</h1>
    <table border="1">
        <tr>
            <th>Location</th>
            <th>Frequency</th>
            <th>Risks</th>
            <th>Timestamp</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['location']; ?></td>
                <td><?php echo $row['frequency']; ?></td>
                <td><?php echo $row['risks']; ?></td>
                <td><?php echo $row['timestamp']; ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
