<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

function fetchStudents($conn, $course = '', $searchTerm = '') {
    $where = [];

    if (!empty($course)) {
        $where[] = "course = ?";
    }

    if (!empty($searchTerm)) {
        $where[] = "(full_name LIKE ?)";
    }

    $whereSql = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';
    $sql = "SELECT * FROM students" . $whereSql;
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $params = [];
        if (!empty($course)) {
            $params[] = $course;
        }
        if (!empty($searchTerm)) {
            $params[] = '%' . $searchTerm . '%'; // Add wildcard for search
        }
        // Bind parameters
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }
    return null;
}

$students = [];
$course = '';

// Handle course filtering
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course = $_POST['course'];
    $students = fetchStudents($conn, $course);
}

// Handle search filtering
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $students = fetchStudents($conn, '', $searchTerm);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="userdashboard.css">
    <title>User Dashboard</title>
</head>

<body>
    <div class="dashboard">
        <div class="sidebar">
            <div class="logo">
                <img src="img/IEAT.jpg" alt="Logo" style="width: 50px; height: 50px;">
            </div>
            <h2>Courses</h2>
            <form method="POST" action="">
                <button type="submit" class="course-btn" name="course" value="BSIT">BSIT</button>
                <button type="submit" class="course-btn" name="course" value="BSABEn">BSABEn</button>
                <button type="submit" class="course-btn" name="course" value="BSFT">BSFT</button>
                <button type="submit" class="course-btn" name="course" value="BSGE">BSGE</button>
            </form>
            <br>
            <div class="logout">
                <form method="POST" action="logout.php">
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>
        <div class="content">
            <h1>IEAT Dashboard</h1>
            <div class="search-container">
                <form method="GET" action="">
                    <input type="text" name="search" placeholder="Search students..." class="search-input">
                    <button type="submit" class="search-btn">Search</button>
                </form>
            </div>
            <div id="table-container">
                <?php if (!empty($students) && $students->num_rows > 0): ?>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Profile Picture</th>
                            <th>Name</th>
                            <th>Year</th>
                            <th>Section</th>
                            <th>Actions</th>
                        </tr>
                        <?php while ($row = $students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><img src="<?php echo htmlspecialchars('uploads/' . ($row['profile_pic'] ?? 'default_profile_pic.jpg')); ?>" alt="Profile Picture" class="profile-pic"></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['year_level']); ?></td>
                                <td><?php echo htmlspecialchars($row['section']); ?></td>
                                <td>
                                    <form method="POST" action="edit_user.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                        <button type="submit" class="action-btn">Edit</button>
                                    </form>
                                    <form method="POST" action="delete.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                        <button type="submit" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this student?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>No students found for this course.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
