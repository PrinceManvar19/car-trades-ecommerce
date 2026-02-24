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
    <title>CarTrade Admin Dashboard</title>
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
            border-radius: 8px;
            padding: 1rem;
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
            <div class="menu-item active" onclick="showSection('dashboard')">Dashboard</div>
            <div class="menu-item" onclick="showSection('cars')">Manage Cars</div>
            <div class="menu-item" onclick="showSection('users')">Manage Users</div>
            <div class="menu-item">
                <a href="Car Buyers List.php" style="color: white; text-decoration: none;">Car Buyers List</a>
            </div>
            <div class="menu-item">
                <a href="Car Selling List.php" style="color: white; text-decoration: none;">Car Selling List</a>
            </div>
            <div class="menu-item">
                <a href="sales-reports.php" style="color: white; text-decoration: none;">Sales Reports</a>
            </div>
            <div class="menu-item" onclick="logout()">Logout</div>
        </div>

        <div class="main-content">
            <div id="dashboard-section">
                <h2>Dashboard Overview</h2>
                <div class="dashboard-cards">
                    <div class="card">
                        <h3>Total Cars</h3>
                        <div class="number">156</div>
                    </div>
                    <div class="card">
                        <h3>Active Users</h3>
                        <div class="number">1,204</div>
                    </div>
                    <div class="card">
                        <h3>Monthly Sales</h3>
                        <div class="number">342K</div>
                    </div>
                    <div class="card">
                        <h3>Pending Approvals</h3>
                        <div class="number">23</div>
                    </div>
                </div>

                <div class="table-container">
                    <h3>Recent Transactions</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Car</th>
                                <th>Buyer</th>
                                <th>Price</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="transactions-table"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const transactions = [
            { id: 1, car: 'Toyota Camry', buyer: 'John Doe', price: 18500, date: '2024-01-05', status: 'Completed' },
            { id: 2, car: 'Honda Civic', buyer: 'Jane Smith', price: 19800, date: '2024-01-04', status: 'Pending' },
            { id: 3, car: 'Ford Fusion', buyer: 'Mike Johnson', price: 16500, date: '2024-01-03', status: 'Completed' }
        ];

        function displayTransactions() {
            const tbody = document.getElementById('transactions-table');
            tbody.innerHTML = transactions.map(t => `
                <tr>
                    <td>${t.id}</td>
                    <td>${t.car}</td>
                    <td>${t.buyer}</td>
                    <td>₹${t.price.toLocaleString()}</td>
                    <td>${t.date}</td>
                    <td>${t.status}</td>
                </tr>
            `).join('');
        }

        displayTransactions();
    </script>
</body>
</html>