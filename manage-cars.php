<?php
session_start();
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

$sql = "SELECT Car_Id, Company, Model, Year, Price FROM table_car_listing";
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
    <title>CarTrade Admin Manage Cars</title>
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

        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
        }

        input, select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background: #3498db;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <div class="admin-logo">CarTrade Admin</div>
            <div class="menu-item" onclick="showSection('dashboard')">Dashboard</div>
            <div class="menu-item active" onclick="showSection('cars')">Manage Cars</div>
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
            <div id="cars-section">
                <h2>Manage Cars</h2>
                <div class="form-container">
                    <form id="carForm" onsubmit="handleCarSubmit(event)">
                        <div class="form-group">
                            <label>Company</label> 
                            <input type="text" id="make" required>
                        </div>
                        <div class="form-group">
                            <label>Model</label>
                            <input type="text" id="model" required>
                        </div>
                        <div class="form-group">
                            <label>Year</label>
                            <input type="number" id="year" required>
                        </div>
                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" id="price" required>
                        </div>
                        <button type="submit">Edit Car</button>
                    </form>
                </div>

                <div class="table-container">
                    <h3>Car Inventory</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Car ID </th>
                                <th>Company</th> 
                                <th>Model</th>
                                <th>Year</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="cars-table"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    function displayCars() {
        const tbody = document.getElementById('cars-table');
        tbody.innerHTML = cars.map(car => `
            <tr>
                <td>${car['Car_Id']}</td>
                <td>${car.Company}</td>
                <td>${car.Model}</td>
                <td>${car.Year}</td>
                <td>₹${car.price.toLocaleString()}</td>
                <td>
                    <button onclick="editCar(${car['Car_Id']})">Edit</button>
                    <button class="delete" onclick="deleteCar(${car['Car-Id']})">Delete</button>
                </td>
            </tr>
        `).join('');
    }

function displayCars() {
    const tbody = document.getElementById('cars-table');
    tbody.innerHTML = cars.map(car => `
        <tr>
            <td>${car['Car_Id']}</td>
            <td>${car.Company}</td>
            <td>${car.Model}</td>
            <td>${car.Year}</td>
            <td>₹${car.Price.toLocaleString()}</td>
            <td>
                <button onclick="editCar(${car['Car_Id']})">Edit</button>
                <button class="delete" onclick="deleteCar(${car['Car_Id']})">Delete</button>
            </td>
        </tr>
    `).join('');
}
    
    displayCars();
</script>
</body>
</html>