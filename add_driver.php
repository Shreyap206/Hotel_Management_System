<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once 'db_connect.php';

$success_msg = '';
$error_msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $name = $_POST['name'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $car_company = $_POST['car_company'];
        $available = $_POST['available'];
        $location = $_POST['location'];

        $sql = "INSERT INTO drivers (name, age, gender, car_company, available, location) 
                VALUES (:name, :age, :gender, :car_company, :available, :location)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':car_company', $car_company);
        $stmt->bindParam(':available', $available);
        $stmt->bindParam(':location', $location);

        if ($stmt->execute()) {
            $success_msg = "Driver added successfully!";
        } else {
            $error_msg = "Failed to add driver. Please try again.";
        }
    } catch (PDOException $e) {
        $error_msg = "Database error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Drivers</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(rgba(40, 100, 200, 0.4), rgba(40, 100, 200, 0.4)), url('assets/images/bg.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .header-title {
            color: #ffffff;
            font-size: 2.5rem;
            font-weight: 400;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
        }

        .split-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            display: flex;
            width: 85%;
            max-width: 1000px;
            height: 550px;
            overflow: hidden;
        }

        .form-pane {
            width: 50%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            overflow-y: auto;
        }

        .form-header {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            color: #2c3e50;
        }

        .image-pane {
            width: 50%;
            background-image: url('assets/images/hotel_driver.png');
            background-size: cover;
            background-position: center;
            background-color: #f8f9fa;
        }

        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 18px;
        }

        .form-row label {
            width: 140px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #0b4b8a; /* Keeping the dark blue text from the screenshot */
        }

        .form-row input[type="text"],
        .form-row input[type="number"],
        .form-row select {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-row input[type="text"]:focus,
        .form-row input[type="number"]:focus,
        .form-row select:focus {
            border-color: #0b4b8a;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .submit-btn, .cancel-btn {
            flex: 1;
            background-color: #000000;
            color: #ffffff;
            padding: 12px 0;
            text-align: center;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .submit-btn:hover, .cancel-btn:hover {
            background-color: #333333;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

    <h1 class="header-title">THE TAJ GROUP WELCOME TO</h1>

    <div class="split-card">
        <div class="form-pane">
            <h2 class="form-header">Add Drivers</h2>

            <?php if ($success_msg): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>
            
            <?php if ($error_msg): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-row">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" required>
                </div>

                <div class="form-row">
                    <label for="age">Age</label>
                    <input type="number" name="age" id="age" required>
                </div>

                <div class="form-row">
                    <label for="gender">Gender</label>
                    <select name="gender" id="gender" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-row">
                    <label for="car_company">Car Company</label>
                    <input type="text" name="car_company" id="car_company" required>
                </div>

                <div class="form-row">
                    <label for="available">Available</label>
                    <select name="available" id="available" required>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>

                <div class="form-row">
                    <label for="location">Location</label>
                    <input type="text" name="location" id="location" required>
                </div>

                <div class="button-group">
                    <button type="submit" class="submit-btn">Add</button>
                    <a href="index.php" class="cancel-btn">Back</a>
                </div>
            </form>
        </div>

        <div class="image-pane">
        </div>
    </div>

</body>
</html>
