<?php
session_start();
include 'db.php';

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate user input
    $full_name = trim($_POST['full_name']);
    $year_level = trim($_POST['year_level']);
    $section = trim($_POST['section']);
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT); // Secure password storage
    $course = trim($_POST['course']);

    // Handle profile picture upload
    $targetDir = "img/"; // Directory where profile pictures will be saved
    $profile_pic = basename($_FILES["profile_pic"]["name"]);
    $targetFilePath = $targetDir . $profile_pic;
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Check if the file is an actual image or fake image
    $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
    if ($check === false) {
        die("File is not an image.");
    }

    // Allow only certain file formats
    $allowedTypes = ['jpg', 'png', 'jpeg', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        die("Only JPG, JPEG, PNG, and GIF files are allowed.");
    }

    // Move uploaded file to target directory
    if (!move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFilePath)) {
        die("Sorry, there was an error uploading your file.");
    }

    // Prepare the SQL statement to insert user data along with profile picture
    $sql = "INSERT INTO students (full_name, year_level, section, username, password, course, profile_pic) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("sssssss", $full_name, $year_level, $section, $username, $password, $course, $profile_pic);
    
    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Redirect to login page after successful signup
        header("Location: login.php?signup=success");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    
    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Link to custom CSS -->
    <title>Signup</title>
</head>
<body>
<div class="container mt-5">
    <h2>Signup</h2>
    <form action="" method="POST" enctype="multipart/form-data"> <!-- Add enctype to handle file uploads -->
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" class="form-control" name="full_name" required>
        </div>
        <div class="form-group">
            <label for="year_level">Year Level</label>
            <input type="text" class="form-control" name="year_level" required>
        </div>
        <div class="form-group">
            <label for="section">Section</label>
            <input type="text" class="form-control" name="section" required>
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <div class="form-group">
            <label for="course">Course</label>
            <select class="form-control" name="course" required>
                <option value="BSIT">BSIT</option>
                <option value="BSABEn">BSABEn</option>
                <option value="BSFT">BSFT</option>
                <option value="BSGE">BSGE</option>
            </select>
        </div>
        <div class="form-group">
            <label for="profile_pic">Profile Picture</label>
            <input type="file" class="form-control" name="profile_pic" required> <!-- Input for file upload -->
        </div>
        <button type="submit" class="btn btn-primary">Signup</button>
    </form>
</div>
</body>
</html>
