<?php
// Database connection
$host = 'localhost';
$dbname = 'eco_track';
$username = 'root'; // Replace with your DB username
$password = ''; // Replace with your DB password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $userRole = $_POST['userRole'];

        if ($userRole === 'planter') {
            $name = $_POST['name'];
            $username = $_POST['username'];
            $phone = $_POST['phone'];
            $phoneOtp = $_POST['phoneOtp'];

            $sql = "INSERT INTO planters (email, name, username, phone, phone_otp) VALUES (:email, :name, :username, :phone, :phoneOtp)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':email' => $email,
                ':name' => $name,
                ':username' => $username,
                ':phone' => $phone,
                ':phoneOtp' => $phoneOtp,
            ]);
        } elseif ($userRole === 'admin') {
            $mineName = $_POST['mineName'];
            $registrationNumber = $_POST['registrationNumber'];
            $mineAddress = $_POST['mineAddress'];
            $username = $_POST['username'];
            $emailOtp = $_POST['emailOtp'];

            $sql = "INSERT INTO admins (email, mine_name, registration_number, mine_address, username, email_otp) VALUES (:email, :mineName, :registrationNumber, :mineAddress, :username, :emailOtp)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':email' => $email,
                ':mineName' => $mineName,
                ':registrationNumber' => $registrationNumber,
                ':mineAddress' => $mineAddress,
                ':username' => $username,
                ':emailOtp' => $emailOtp,
            ]);
        }

        echo "Registration successful!";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
