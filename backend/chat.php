<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender = $_POST['sender'];
    $receiver = $_POST['receiver'];
    $message = $_POST['message'];

    $sql = "INSERT INTO messages (sender, receiver, message) VALUES ('$sender', '$receiver', '$message')";
    if ($conn->query($sql) === TRUE) {
        echo "Message sent!";
    } else {
        echo "Error: " . $conn->error;
    }
}

$sql = "SELECT * FROM messages ORDER BY timestamp ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
</head>
<body>
    <h1>Chat System</h1>
    <form method="POST" action="">
        <label for="sender">Your Name:</label>
        <input type="text" name="sender" required><br><br>

        <label for="receiver">Receiver:</label>
        <input type="text" name="receiver" required><br><br>

        <label for="message">Message:</label>
        <textarea name="message" required></textarea><br><br>

        <button type="submit">Send</button>
    </form>

    <h2>Messages</h2>
    <div>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <p><b><?php echo $row['sender']; ?>:</b> <?php echo $row['message']; ?> <i>(to <?php echo $row['receiver']; ?>)</i></p>
        <?php } ?>
    </div>
</body>
</html>
