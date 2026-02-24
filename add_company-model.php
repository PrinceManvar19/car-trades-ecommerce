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

$query = "SELECT * FROM C_company";
$result = $conn->query($query);
$companies = array();
while ($row = $result->fetch_assoc()) {
    $companies[] = $row;
}

if (isset($_POST['add_company'])) {
    $company_name = isset($_POST['company_name']) ? $_POST['company_name'] : '';
    if (!empty($company_name)) {
        $stmt = $conn->prepare("INSERT INTO C_company (Company_Name) VALUES (?)");
        $stmt->bind_param("s", $company_name);
        if ($stmt->execute()) {
            echo "Company added successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

if (isset($_POST['add_model'])) {
    $company_id = isset($_POST['company_id']) ? $_POST['company_id'] : '';
    $model_name = isset($_POST['model_name']) ? $_POST['model_name'] : '';
    
    if (!empty($company_id) && !empty($model_name)) {
        $company_id = filter_var($company_id, FILTER_SANITIZE_NUMBER_INT);
        $stmt = $conn->prepare("INSERT INTO C_model (Company_Id, Model_Name) VALUES (?, ?)");
        $stmt->bind_param("is", $company_id, $model_name);
        if ($stmt->execute()) {
            echo "Model added successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
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

        form {
            margin-top: 2rem;
            padding: 1rem;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 1rem;
        }

        input[type="text"], select {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button[type="submit"] {
            background: #3498db;
            color: #fff;
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background: #2c3e50;
        }
    </style>

</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <div class="admin-logo">CarTrade Admin</div>
            <div class="menu-item active">Dashboard</div>
            <div class="menu-item"><a href="add_company-model.php" style="color: white; text-decoration: none;">Add Car Company and Model</a></div>
            <div class="menu-item"><a href="Test Drive Requests.php" style="color: white; text-decoration: none;">Test Drive Requests</a></div>
            <div class="menu-item"><a href="Document Verification Requests.php" style="color: white; text-decoration: none;">Document Verification Request</a></div>
            <div class="menu-item"><a href="Car Listing Requests.php" style="color: white; text-decoration: none;">Car Listing Requests</a></div>
            <div class="menu-item" ><a href="pay to seller.php" style="color: white; text-decoration: none;">Pay to seller </a></div>
            <div class="menu-item" onclick="logout()">Logout</div>
        </div>

        <div class="main-content">
            <h2>Add Car Company and Model</h2>

            <h3>Add Car Company</h3>
            <form action="" method="post">
                <input type="text" name="company_name" placeholder="Company Name" required>
                <button type="submit" name="add_company">Add Company</button>
            </form>

            <h3>Add Car Model</h3>
            <form action="" method="post">
<select name="company_id" required>
                    <?php foreach ($companies as $company) { ?>
                        <option value="<?php echo $company['id']; ?>"><?php echo $company['Company_Name']; ?></option>
                    <?php } ?>
                </select>
                <input type="text" name="model_name" placeholder="Model Name" required>
                <button type="submit" name="add_model">Add Model</button>
            </form>
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