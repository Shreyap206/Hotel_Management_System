<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
// Include the database connection if needed in the future
require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reception</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            /* Background image with a slight blue tint/overlay */
            background: linear-gradient(rgba(40, 100, 200, 0.4), rgba(40, 100, 200, 0.4)), url('assets/images/bg.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
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

        .reception-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            display: flex;
            width: 80%;
            max-width: 1000px;
            height: 600px;
            overflow: hidden;
        }

        .sidebar {
            width: 35%;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background-color: #ffffff;
            overflow-y: auto;
        }

        .sidebar-btn {
            background-color: #000000;
            color: #ffffff;
            padding: 12px 15px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 2px;
            transition: background-color 0.2s, transform 0.1s;
            border: none;
            cursor: pointer;
            display: block;
            width: 100%;
        }

        .sidebar-btn:hover {
            background-color: #333333;
            transform: scale(1.02);
        }

        .main-image-area {
            width: 65%;
            position: relative;
            background-image: url('assets/images/reception_interior.png');
            background-size: cover;
            background-position: center;
        }

        /* The text overlay inside the main image area */
        .overlay-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            /* White background with some transparency might look better, 
               but user's image shows the text directly on the image. */
        }

        .overlay-text .word-hotel {
            color: #4CAF50; /* Green */
            font-size: 4.5rem;
            font-weight: 300;
            display: block;
            line-height: 1;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.8);
        }

        .overlay-text .word-reception {
            color: #000000; /* Black */
            font-size: 4rem;
            font-weight: 300;
            display: block;
            line-height: 1;
            margin-top: -5px;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.8);
        }

        /* Custom scrollbar for sidebar if content is too long */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #888; 
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #555; 
        }
    </style>
</head>
<body>

    <h1 class="header-title">THE TAJ GROUP WELCOME TO</h1>

    <div class="reception-card">
        <div class="sidebar">
            <a href="add_room.php" class="sidebar-btn">Add Room</a>
            <a href="add_employee.php" class="sidebar-btn">Add Employee</a>
            <a href="add_driver.php" class="sidebar-btn">Add Driver</a>
            <a href="new_customer.php" class="sidebar-btn">New Customer Form</a>
            <a href="rooms.php" class="sidebar-btn">Rooms</a>
            <a href="department.php" class="sidebar-btn">Department</a>
            <a href="all_employee.php" class="sidebar-btn">All Employee</a>
            <a href="customer_info.php" class="sidebar-btn">Customer Info</a>
            <a href="manager_info.php" class="sidebar-btn">Manager Info</a>
            <a href="checkout.php" class="sidebar-btn">Checkout</a>
            <a href="update_status.php" class="sidebar-btn">Update Status</a>
            <a href="update_room_status.php" class="sidebar-btn">Update Room Status</a>
            <a href="pickup_service.php" class="sidebar-btn">Pickup Service</a>

            <a href="logout.php" class="sidebar-btn" style="margin-top: auto;">Logout</a>
        </div>

        <div class="main-image-area">
            <div class="overlay-text">
                <span class="word-hotel">HOTEL</span>
                <span class="word-reception">RECEPTION</span>
            </div>
        </div>
    </div>

</body>
</html>
