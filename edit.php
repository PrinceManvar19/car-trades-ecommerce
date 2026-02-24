<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'carstreads');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['Car_Id'])) {
    header('Location: your-cars.php');
    exit;
}

$car_id = filter_var($_GET['Car_Id'], FILTER_SANITIZE_NUMBER_INT);

$stmt = $conn->prepare("SELECT * FROM table_car_listing WHERE Car_Id = ?");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Car not found.";
    exit;
}

if (isset($_POST['edit'])) {
    $car_id = $_GET['Car_Id'];
    $carCompany = $_POST['carCompany'];
    $carModel = $_POST['carModel'];
    $carYear = $_POST['carYear'];
    $carexpected_price = $_POST['carexpected_price'];
    $mileage = $_POST['mileage'];
    $location = $_POST['location'];
    $carDescription = $_POST['carDescription'];

    $stmt = $conn->prepare("UPDATE table_car_listing SET Company = ?, Model = ?, Year = ?, expected_price = ?, Mileage = ?, Location = ?, Description = ? WHERE Car_Id = ?");
    $stmt->bind_param("ssidsssi", $carCompany, $carModel, $carYear, $carexpected_price, $mileage, $location, $carDescription, $car_id);
    $result = $stmt->execute();

    if ($result) {
        $_SESSION['message'] = "Car details updated successfully.";
        header('Location: your-cars.php');
        exit;
    } else {
        echo "Error updating car details: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarTrade - Edit Car</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

nav {
    background: #1a1a1a;
    padding: 1rem;
    color: white;
}

.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-size: 1.5rem;
    font-weight: bold;
}

.nav-links a {
    color: white;
    text-decoration: none;
    margin-left: 2rem;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropbtn {
    background-color: rgba(25, 29, 26, 0);
    color: white;
    padding: 16px;
    font-size: 16px;
    border: none;
    cursor: pointer;
}

.dropbtn:hover {
    background-color: rgba(45, 45, 45, 0);
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {
    background-color: #f1f1f1;
}

.dropdown:hover .dropdown-content {
    display: block;
}

.dropdown:hover .dropbtn {
    background-color: rgba(66, 66, 66, 0);
}

.container {
    margin-top: 50px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.form-container {
    max-width: 600px;
    margin: 2rem auto;
    padding: 2rem;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.form-group {
    margin-bottom: 1rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
}

input,
select,
textarea {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

button {
    background: #e74c3c;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background: #c0392b;
}

a {
    color: #007BFF;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

.row {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

.col-md-6 {
    flex-basis: 50%;
    padding: 20px;
}

.car-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 10px;
}

.car-details {
    padding: 20px;
}

.car-details h3 {
    font-size: 24px;
    margin-bottom: 10px;
}

.car-details p {
    font-size: 18px;
    margin-bottom: 10px;
}

.cars-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    padding: 1rem;
}

.car-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.car-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
}

.car-details {
    margin-top: 10px;
    text-align: left;
}

.car-details h3 {
    font-size: 1.2rem;
    margin-bottom: 5px;
}

.car-documents {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    flex-wrap: wrap;
    justify-content: center;
}

.document-item {
    text-align: center;
}

.doc-image {
    width: 80px;
    height: 60px;
    object-fit: cover;
    border: 1px solid #ccc;
    border-radius: 4px;
}
</style>
<body>
    <nav>
        <div class="nav-container">
            <div class="logo">CarTrade</div>
            <div class="nav-links">
                <a href="homepage.php">Home</a>
                <a href="#buy">Buy Car</a>
                <a href="#sell">Sell Car</a>
                <div class="dropdown">
                    <button class="dropbtn"><?php echo isset($_SESSION['username']) ? 'Hello, ' . $_SESSION['username'] . ' :)' : 'Account'; ?></button>
                    <div class="dropdown-content">
                        <?php if (isset($_SESSION['username'])) { ?>
                            <a href="your-cars.php">Your Cars</a>
                            <a href="details.php">Details</a>
                            <a href="settings.php">Settings</a>
                            <a href="logout.php">Logout</a>
                        <?php } else { ?>
                            <a href="login.php">Login</a>
                            <a href="registration.php">Signup</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Edit Car</h2>
        <div class="form-container">
            <form action="edit.php?Car_Id=<?php echo $car_id; ?>" method="post">
                <div class="form-group">
                    <label for="carCompany">Company</label>
                    <input type="text" id="carCompany" name="carCompany" value="<?php echo $row['Company']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="carModel">Model</label>
                    <input type="text" id="carModel" name="carModel" value="<?php echo $row['Model']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="carYear">Year</label>
                    <input type="number" id="carYear" name="carYear" value="<?php echo $row['Year']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="carPrice">Expected Price</label>
                    <input type="number" id="carexpected_price" name="carexpected_price" value="<?php echo $row['expected_price']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="mileage">Mileage (KM)</label>
                    <input type="number" id="mileage" name="mileage" value="<?php echo $row['Mileage']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="<?php echo $row['Location']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="carDescription">Description</label>
                    <textarea id="carDescription" name="carDescription" rows="4" required><?php echo $row['Description']; ?></textarea>
                </div>
                <button type="submit" name="edit">Edit</button>
            </form>
        </div>
    </div>
</body>
</html>