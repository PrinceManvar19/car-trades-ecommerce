<?php
$host = 'localhost';
$username = 'root';
$password = '';
$db_name = 'carstreads';

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function get_cars() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM table_car_listing WHERE status = 'Approved'");
    $stmt->execute();
    $result = $stmt->get_result();
    $cars = array();
    while ($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
    $stmt->close();
    return $cars;
}

function add_car($make, $model, $year, $price, $description, $image) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO table_car_listing (Company, Model, Year, expected_price, Description, PhotoURL1, Status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("ssidss", $make, $model, $year, $price, $description, $image);
    $stmt->execute();
    $stmt->close();
}

function handle_buy($car_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM table_car_listing WHERE Car_Id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();
    if ($car) {
        echo "Thank you for purchasing the " . $car['Year'] . " " . $car['Company'] . " " . $car['Model'] . " for $" . $car['expected_price'];
    }
    $stmt->close();
}

function handle_sell_submit($Company, $Model, $Year, $Price, $Description, $Image) {
    global $conn;
    add_car($Company, $Model, $Year, $Price, $Description, $Image);
    echo "Thank you for submitting your car! Our team will review your listing.";
}

function upload_photo($car_id, $Image) {
    global $conn;
    if ($Image['error'] === 0) {
        $fileExtension = pathinfo($Image['name'], PATHINFO_EXTENSION);

        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $filename = uniqid() . '.' . $fileExtension;

            move_uploaded_file($Image['tmp_name'], 'uploads/' . $filename);

            $stmt = $conn->prepare("INSERT INTO photo_upload (car_id, photoURL, UploadDate) VALUES (?, ?, NOW())");
            $stmt->bind_param("is", $car_id, $filename);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error: Only image files are allowed.";
            exit;
        }
    } else {
        echo "Error: Image upload failed.";
        exit;
    }
}
?>
