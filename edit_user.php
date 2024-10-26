<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Initialize variables for the user data
$user = [];
$user_id = null;

// Check if an ID is set for editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $user_id = $_POST['id'];

    // Fetch user data from the database
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "User not found.";
        exit();
    }
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $full_name = $_POST['full_name'];
    $year_level = $_POST['year_level'];
    $section = $_POST['section'];
    $profile_pic = $_FILES['profile_pic']['name'];
    $target_dir = "uploads/";

    // File upload logic
    if (!empty($profile_pic)) {
        $target_file = $target_dir . basename($profile_pic);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file);
    } else {
        // If no new profile pic, retain the old one
        $profile_pic = $user['profile_pic'];
    }

    // Update user data
    $stmt = $conn->prepare("UPDATE students SET full_name = ?, year_level = ?, section = ?, profile_pic = ? WHERE id = ?");
    $stmt->bind_param('ssssi', $full_name, $year_level, $section, $profile_pic, $user_id);
    $stmt->execute();

    // Redirect to the dashboard after update
    header('Location: userdashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin-top: 20px;
            background-color: #f2f6fc;
            color: #69707a;
        }
        .img-account-profile {
            height: 10rem;
        }
        .rounded-circle {
            border-radius: 50% !important;
        }
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgb(33 40 50 / 15%);
        }
        .card .card-header {
            font-weight: 500;
        }
        .card-header {
            padding: 1rem 1.35rem;
            margin-bottom: 0;
            background-color: rgba(33, 40, 50, 0.03);
            border-bottom: 1px solid rgba(33, 40, 50, 0.125);
        }
        .form-control {
            border-radius: 0.35rem;
        }
        .nav-borders .nav-link.active {
            color: #0061f2;
            border-bottom-color: #0061f2;
        }
        .nav-borders .nav-link {
            color: #69707a;
            border-bottom-width: 0.125rem;
            border-bottom-style: solid;
            border-bottom-color: transparent;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            margin-left: 1rem;
            margin-right: 1rem;
        }
        .profile-picture-circle {
    display: flex; /* Use flexbox to center the image */
    justify-content: center; /* Center horizontally */
    align-items: center; /* Center vertically */
    width: 100px; /* Set a width for the circle */
    height: 100px; /* Set a height for the circle */
    border: 4px solid #0061f2; /* Border color */
    border-radius: 50%; /* Make it circular */
    overflow: hidden; /* Hide overflow for the image */
    margin: 0 auto; /* Center the circle on the page */
}

.img-account-profile {
    width: 100%; /* Make the image fill the circle */
    height: auto; /* Maintain aspect ratio */
    object-fit: cover; /* Cover the entire circle */
}


    </style>
</head>
<body>
    <div class="container-xl px-4 mt-4">
        <!-- Account page navigation -->
        <nav class="nav nav-borders">
    <div class="circle-border">
        <a class="nav-link active ms-0" href="edit_user.php">Profile</a>
    </div>
</nav>

        <hr class="mt-0 mb-4">
        <div class="row">
            <div class="col-xl-4">
                <!-- Profile picture card -->
                <div class="card mb-4 mb-xl-0">
                    <div class="card-header">Profile Picture</div>
                    <div class="card-body text-center">
                        <!-- Profile picture image -->
                        <div class="profile-picture-circle">
    <img class="img-account-profile" src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture">
</div>
                        <div class="small font-italic text-muted mb-4">JPG or PNG no larger than 5 MB</div>
                        <!-- Profile picture upload button -->
                        <input type="file" name="profile_pic" class="form-control mb-2">
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <!-- Account details card -->
                <div class="card mb-4">
                    <div class="card-header">Account Details</div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user_id); ?>">
                            <!-- Form Group (full name) -->
                            <div class="mb-3">
                                <label class="small mb-1" for="full_name">Full Name</label>
                                <input class="form-control" id="full_name" type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            <!-- Form Row -->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (year level) -->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="year_level">Year Level</label>
                                    <input class="form-control" id="year_level" type="text" name="year_level" value="<?php echo htmlspecialchars($user['year_level']); ?>" required>
                                </div>
                                <!-- Form Group (section) -->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="section">Section</label>
                                    <input class="form-control" id="section" type="text" name="section" value="<?php echo htmlspecialchars($user['section']); ?>" required>
                                </div>
                            </div>
                            <!-- Save changes button -->
                            <button class="btn btn-primary" type="submit" name="update">Update User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
