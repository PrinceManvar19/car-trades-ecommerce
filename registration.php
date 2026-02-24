<?php
session_start();

$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'carstreads';

$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Username = $_POST["Username"];
    $Email = $_POST["Email"];
    $Phone = $_POST["Phone"];
    $Password = $_POST["Password"];
    $confirm_password = $_POST['confirm_password'];
    $Usertype = $_POST["Usertype"];

    $Usertype = trim($Usertype);

    if (empty($Username) || empty($Email) || empty($Phone) || empty($Password) || empty($Usertype)) {
        $error = "Please fill in all fields.";
    } else if ($Password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $password_hash = password_hash($Password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO table_user (Username, Email, Phone, Password, Usertype) VALUES (?, ?, ?, ?, ?)");
        if ($stmt === false) {
            $error = "Database error: " . $conn->error . " Please ensure the database tables are set up by running setup_database.php";
        } else {
            $stmt->bind_param("sssss", $Username, $Email, $Phone, $password_hash, $Usertype);

if ($stmt->execute()) {
                $_SESSION["username"] = $Username;
                $_SESSION["usertype"] = $Usertype;
                if ($Usertype == "Admin") {
                    header("Location: adminpage2.php");
                } elseif ($Usertype == "User") {
                    header("Location: homepage.php");
                } else {
                    header("Location: profile.php?username=$Username");
                }
                exit;
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .submit-button {
            width: 100%;
            padding: 10px;
            background-color: #ff2121;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit-button:hover {
            background-color: #ff2121;
        }

        .toggle-link {
            text-align: center;
            margin-top: 20px;
        }

        .toggle-link a {
            color: #007bff;
            text-decoration: none;
        }

        .toggle-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="Username">Username:</label>
                <input type="text" id="Username" name="Username" required>
            </div>
            <div class="form-group">
                <label for="Email">Email:</label>
                <input type="email" id="Email" name="Email" required>
            </div>
            <div class="form-group">
                <label for="Phone">Phone Number:</label>
                <input type="tel" id="Phone" name="Phone" required>
            </div>
            <div class="form-group">
                <label for="Password">Password:</label>
                <input type="password" id="Password" name="Password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <label for="Usertype">User Type:</label>
                <select id="Usertype" name="Usertype" required>
                    <option value="User">User</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="submit-button">Register</button>
        </form>
        <div class="toggle-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
