<?php
session_start();
include ("db.php");

if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit();
}


$username = $_SESSION['user_id'];
$query = "SELECT full_name, address, num_vehicles FROM profiles WHERE Account_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $username);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $full_name = $row['full_name'];
    $address = $row['address'];
    $num_vehicles = $row['num_vehicles'];
} else {
   
    $full_name = "";
    $address = "";
    $num_vehicles = "";
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="profile-container">
        <form action="updateProfile.php" method="post">
            <h1>Edit Profile</h1>
            <div class="profile-details">
                <div class="profile-info">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo $full_name; ?>">
                </div>
                <div class="profile-info">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" value="<?php echo $address; ?>">
                </div>
                <div class="profile-info">
                    <label for="num_vehicles">Number of Vehicles:</label>
                    <input type="number" id="num_vehicles" name="num_vehicles" value="<?php echo $num_vehicles; ?>">
                </div>
            </div>
            <button type="submit" name="submit">Save Changes</button>
            <button><a href="profile.php" class="back-link">Back to Profile</a></button>
        </form>
    </div>
</body>

</html>