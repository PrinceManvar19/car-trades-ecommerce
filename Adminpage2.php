<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] != 'Admin') {
    die('Unauthorized access - Admin only');
}

$conn = new mysqli('localhost', 'root', '', 'carstreads');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM table_car_listing";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $cars = array();
    
    while($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
    
    echo '<script>const cars = ' . json_encode($cars) . ';</script>';
} else {
    echo '<script>const cars = [];</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarTrade Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 1rem;
        }

        .admin-logo {
            font-size: 1.5rem;
            padding: 1rem;
            border-bottom: 1px solid #34495e;
        }

        .menu-item {
            padding: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .menu-item:hover {
            background: #34495e;
        }

        .menu-item.active {
            background: #3498db;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            background: #f5f6fa;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card h3 {
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        .card .number {
            font-size: 2rem;
            color: #e41818;
        }

        .table-container {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <div class="admin-logo">CarTrade Admin</div>
            <div class="menu-item active" >Dashboard</div>
            <div class="menu-item"><a href="add_company-model.php" style="color: white; text-decoration: none;">Add Car Company and Model</a></div>
            <div class="menu-item"><a href="Test Drive Requests.php" style="color: white; text-decoration: none;">Test Drive Requests</a></div>
            <div class="menu-item"><a href="Document Verification Requests.php" style="color: white; text-decoration: none;">Document Verification Request</a></div>
            <div class="menu-item"><a href="Car Listing Requests.php" style="color: white; text-decoration: none;">Car Listing Requests</a></div>
            <div class="menu-item" ><a href="pay to seller.php" style="color: white; text-decoration: none;">Pay to seller </a></div>
            <div class="menu-item" onclick="logout()">Logout</div>
        </div>

        <div class="main-content">
            <div id="dashboard-section">
                <h2>Dashboard Overview</h2>
                <div class="dashboard-cards">
                <div class="card">
                <div class="card">
                <?php
$sql = "SELECT COUNT(*) as total_cars FROM table_car_listing";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_cars = $row['total_cars'];
} else {
    $total_cars = 0;
}
?>
    <h3>Total Cars</h3>
    <div class="number"><?php echo $total_cars; ?></div>
</div>

<div class="card">
        <h3>Active Users</h3>
        <div class="number"></div>
    </div>
             </div>
                <div class="table-container">
    <h3>Recent Cars Listed</h3>
    <table>
        <thead>
            <tr>
                <th>Car ID</th>
                <th>Model</th>
                <th>Year</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($cars)): ?>
            <?php foreach ($cars as $car): ?>
            <tr>
                <td><?php echo $car['Car_Id'] ?? ''; ?></td>
                <td><?php echo $car['Model'] ?? ''; ?></td>
                <td><?php echo $car['Year'] ?? ''; ?></td>
                <td><?php echo 'Rs' . number_format($car['Price'] ?? 0, 2); ?></td>
                <td><?php echo $car['status'] ?? ''; ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="5">No cars listed.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
            </div>
        </div>
    </div>

    <script>
        function showSection(section) {
            document.querySelectorAll('.menu-item').forEach(item => item.classList.remove('active'));
            event.target.classList.add('active');
            document.querySelectorAll('[id$="-section"]').forEach(div => div.style.display = 'none');

            const selectedSection = document.getElementById(`${section}-section`);
            if (selectedSection) {
                selectedSection.style.display = 'block';
            } else {
                console.error(`Section with ID '${section}-section' not found!`);
            }
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'homepage.php'; 
            }
        }
    </script>
</body>
</html>