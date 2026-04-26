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
        // Fetch room prices for backend validation
        $room_prices = [];
        $stmt_prices = $conn->query("SELECT room_number, price FROM rooms");
        while ($row = $stmt_prices->fetch(PDO::FETCH_ASSOC)) {
            $room_prices[$row['room_number']] = (float)$row['price'];
        }
        $id_type = $_POST['id_type'];
        $id_number = $_POST['id_number'];
        $name = $_POST['name'];
        $gender = $_POST['gender'];
        $country = $_POST['country'];
        $phone_number = $_POST['phone_number'];
        $address = $_POST['address'];
        $room_type = $_POST['room_type'];
        $room_number = $_POST['room_number'];
        $number_of_guests = $_POST['number_of_guests'];
        $checkin_time = $_POST['checkin_time'];
        $checkout_date = $_POST['checkout_date'];
        $deposit = (float)$_POST['deposit'];

        // Calculate remaining price securely on backend
        $price_per_night = isset($room_prices[$room_number]) ? $room_prices[$room_number] : 0;
        
        $ci_date = new DateTime($checkin_time);
        $co_date = new DateTime($checkout_date);
        $days = $ci_date->diff($co_date)->days;
        if ($days < 1) $days = 1;

        $total_price = $days * $price_per_night;
        $remaining_price = $total_price - $deposit;

        $sql = "INSERT INTO customers (id_type, id_number, name, gender, country, phone_number, address, room_type, room_number, number_of_guests, checkin_time, checkout_date, deposit, remaining_price) 
                VALUES (:id_type, :id_number, :name, :gender, :country, :phone_number, :address, :room_type, :room_number, :number_of_guests, :checkin_time, :checkout_date, :deposit, :remaining_price)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_type', $id_type);
        $stmt->bindParam(':id_number', $id_number);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':room_type', $room_type);
        $stmt->bindParam(':room_number', $room_number);
        $stmt->bindParam(':number_of_guests', $number_of_guests);
        $stmt->bindParam(':checkin_time', $checkin_time);
        $stmt->bindParam(':checkout_date', $checkout_date);
        $stmt->bindParam(':deposit', $deposit);
        $stmt->bindParam(':remaining_price', $remaining_price);

        if ($stmt->execute()) {
            $success_msg = "Customer record registered successfully!";
        } else {
            $error_msg = "Failed to register customer. Please try again.";
        }
    } catch (PDOException $e) {
        $error_msg = "Database error: " . $e->getMessage();
    }
}

// Fetch room prices for frontend JS calculation
$room_prices_json = '{}';
try {
    $stmt = $conn->query("SELECT room_number, price FROM rooms");
    $prices = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $prices[$row['room_number']] = (float)$row['price'];
    }
    $room_prices_json = json_encode($prices);
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Customer Form</title>
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
            padding: 40px 20px;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 900px;
            margin-bottom: 20px;
        }

        .back-btn {
            background-color: #000000;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: background-color 0.2s;
        }

        .back-btn:hover {
            background-color: #333333;
        }

        .page-title {
            color: #ffffff;
            font-size: 2rem;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
        }

        .form-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 900px;
            padding: 40px;
            backdrop-filter: blur(10px);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
        }

        input, select, textarea {
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.2s;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #4CAF50;
        }

        .submit-btn {
            grid-column: 1 / -1;
            background-color: #000000;
            color: #ffffff;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.2s, transform 0.1s;
        }

        .submit-btn:hover {
            background-color: #333333;
            transform: scale(1.01);
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-weight: 500;
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

        /* Responsive */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <div class="header-container">
        <h1 class="page-title">NEW CUSTOMER FORM</h1>
        <a href="reception.php" class="back-btn">Back to Reception</a>
    </div>

    <div class="form-card">
        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        
        <?php if ($error_msg): ?>
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-grid">
                
                <div class="form-group">
                    <label for="id_type">ID Type *</label>
                    <select name="id_type" id="id_type" required>
                        <option value="" disabled selected>Select ID Document</option>
                        <option value="Aadhar Card">Aadhar Card</option>
                        <option value="Passport">Passport</option>
                        <option value="Driving License">Driving License</option>
                        <option value="Voter ID">Voter ID</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_number">ID Number *</label>
                    <input type="text" name="id_number" id="id_number" placeholder="Enter ID Number" required>
                </div>

                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" name="name" id="name" placeholder="Enter full name" required>
                </div>

                <div class="form-group">
                    <label for="gender">Gender *</label>
                    <select name="gender" id="gender" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="country">Country *</label>
                    <input type="text" name="country" id="country" placeholder="Enter Country" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number *</label>
                    <input type="text" name="phone_number" id="phone_number" placeholder="Enter phone number" required>
                </div>

                <div class="form-group full-width">
                    <label for="address">Address (Optional)</label>
                    <textarea name="address" id="address" rows="2" placeholder="Enter residential address"></textarea>
                </div>

                <div class="form-group">
                    <label for="room_type">Room Type *</label>
                    <select name="room_type" id="room_type" required>
                        <option value="" disabled selected>Select Room Type</option>
                        <option value="Single">Single</option>
                        <option value="Double">Double</option>
                        <option value="Suite">Suite</option>
                        <option value="Deluxe">Deluxe</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="room_number">Allocated Room Number *</label>
                    <input type="text" name="room_number" id="room_number" placeholder="e.g. 101" required>
                </div>

                <div class="form-group">
                    <label for="number_of_guests">Number of Guests *</label>
                    <input type="number" name="number_of_guests" id="number_of_guests" min="1" value="1" required>
                </div>

                <div class="form-group">
                    <label for="checkin_time">Check-in Time *</label>
                    <input type="datetime-local" name="checkin_time" id="checkin_time" required>
                </div>

                <div class="form-group">
                    <label for="checkout_date">Check-out Date *</label>
                    <input type="date" name="checkout_date" id="checkout_date" required>
                </div>

                <div class="form-group">
                    <label for="deposit">Deposit Amount ($) *</label>
                    <input type="number" step="0.01" name="deposit" id="deposit" placeholder="Enter deposit amount" required>
                </div>

                <div class="form-group">
                    <label for="remaining_price">Remaining Price ($)</label>
                    <input type="text" id="remaining_price" readonly placeholder="Auto-calculated" style="background-color: #f5f5f5; cursor: not-allowed;">
                </div>

                <button type="submit" class="submit-btn">Register Customer</button>
            </div>
        </form>
    </div>

    <script>
        const roomPrices = <?php echo $room_prices_json; ?>;
        
        const roomNumberInput = document.getElementById('room_number');
        const checkinInput = document.getElementById('checkin_time');
        const checkoutInput = document.getElementById('checkout_date');
        const depositInput = document.getElementById('deposit');
        const remainingPriceInput = document.getElementById('remaining_price');

        function calculateRemainingPrice() {
            const roomNum = roomNumberInput.value;
            const checkin = new Date(checkinInput.value);
            const checkout = new Date(checkoutInput.value);
            const deposit = parseFloat(depositInput.value) || 0;

            if (roomPrices[roomNum] && checkinInput.value && checkoutInput.value) {
                // Calculate days difference
                const timeDiff = checkout.getTime() - checkin.getTime();
                let days = Math.ceil(timeDiff / (1000 * 3600 * 24));
                if (days < 1) days = 1;

                const pricePerNight = roomPrices[roomNum];
                const totalPrice = days * pricePerNight;
                const remaining = totalPrice - deposit;
                
                remainingPriceInput.value = remaining.toFixed(2);
            } else {
                remainingPriceInput.value = '';
            }
        }

        roomNumberInput.addEventListener('input', calculateRemainingPrice);
        checkinInput.addEventListener('change', calculateRemainingPrice);
        checkoutInput.addEventListener('change', calculateRemainingPrice);
        depositInput.addEventListener('input', calculateRemainingPrice);
    </script>
</body>
</html>
