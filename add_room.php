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
        $room_number = $_POST['room_number'];
        $availability = $_POST['availability'];
        $status = $_POST['status'];
        $price = $_POST['price'];
        $bed_type = $_POST['bed_type'];

        // Check if room number already exists
        $check_stmt = $conn->prepare("SELECT * FROM rooms WHERE room_number = :room_number");
        $check_stmt->bindParam(':room_number', $room_number);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            $error_msg = "Room number already exists. Please enter a different room number.";
        } else {
            $sql = "INSERT INTO rooms (room_number, availability, status, price, bed_type) 
                    VALUES (:room_number, :availability, :status, :price, :bed_type)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':room_number', $room_number);
            $stmt->bindParam(':availability', $availability);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':bed_type', $bed_type);

            if ($stmt->execute()) {
                $success_msg = "Room added successfully!";
            } else {
                $error_msg = "Failed to add room. Please try again.";
            }
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
    <title>Add Room</title>
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
            max-width: 900px;
            height: 500px;
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
            background-image: url('assets/images/room_interior.png');
            background-size: cover;
            background-position: center;
            background-color: #f8f9fa;
        }

        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .form-row label {
            width: 130px;
            font-size: 0.95rem;
            font-weight: 500;
            color: #333;
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
            margin-top: 10px;
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
            <h2 class="form-header">Add Rooms</h2>

            <?php if ($success_msg): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>
            
            <?php if ($error_msg): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-row">
                    <label for="room_number">Room Number :</label>
                    <input type="text" name="room_number" id="room_number" required>
                </div>

                <div class="form-row">
                    <label for="availability">Available :</label>
                    <select name="availability" id="availability" required>
                        <option value="Available">Available</option>
                        <option value="Occupied">Occupied</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>

                <div class="form-row">
                    <label for="status">Cleaning Status :</label>
                    <select name="status" id="status" required>
                        <option value="Cleaned">Cleaned</option>
                        <option value="Dirty">Dirty</option>
                    </select>
                </div>

                <div class="form-row">
                    <label for="price">Price :</label>
                    <input type="number" step="0.01" name="price" id="price" required>
                </div>

                <div class="form-row">
                    <label for="bed_type">Bed Type :</label>
                    <select name="bed_type" id="bed_type" required>
                        <option value="Single Bed">Single Bed</option>
                        <option value="Double Bed">Double Bed</option>
                        <option value="Suite">Suite</option>
                        <option value="Deluxe">Deluxe</option>
                    </select>
                </div>

                <div class="button-group">
                    <button type="submit" class="submit-btn">Add Room</button>
                    <a href="index.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>

        <div class="image-pane">
        </div>
    </div>

</body>
</html>
