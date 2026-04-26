<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once 'db_connect.php';

$success_msg = '';
$error_msg = '';

// Fetch all living customers for the dropdown
$customers = [];
try {
    $stmt = $conn->query("SELECT id_number, room_number, name, checkin_time, deposit, remaining_price FROM customers WHERE status = 'Living'");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Database error: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    try {
        $doc_number = $_POST['customer_id'];
        $room_number = $_POST['room_number'];
        $name = $_POST['name'];
        $deposit = $_POST['deposit'];
        $remaining_price = $_POST['remaining_price'];

        // Update customer details
        $upd_stmt = $conn->prepare("UPDATE customers SET room_number = :room_num, name = :name, deposit = :deposit, remaining_price = :remaining WHERE id_number = :doc_num");
        $upd_stmt->bindParam(':room_num', $room_number);
        $upd_stmt->bindParam(':name', $name);
        $upd_stmt->bindParam(':deposit', $deposit);
        $upd_stmt->bindParam(':remaining', $remaining_price);
        $upd_stmt->bindParam(':doc_num', $doc_number);
        
        if ($upd_stmt->execute()) {
            $success_msg = "Status updated successfully!";
            
            // Refresh customer list
            $stmt = $conn->query("SELECT id_number, room_number, name, checkin_time, deposit, remaining_price FROM customers WHERE status = 'Living'");
            $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error_msg = "Failed to update status.";
        }

    } catch (PDOException $e) {
        $error_msg = "Update failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status</title>
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

        .split-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            display: flex;
            width: 85%;
            max-width: 950px;
            height: 550px;
            overflow: hidden;
            margin-top: 20px;
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
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 30px;
            color: #0b4b8a; /* Blue header matching screenshot */
        }

        .image-pane {
            width: 50%;
            background-image: url('assets/images/update_status.png');
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
            width: 140px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #333;
        }

        .form-row input[type="text"],
        .form-row input[type="number"],
        .form-row select {
            flex: 1;
            padding: 8px 12px;
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

        .form-row input[readonly] {
            background-color: #f5f5f5;
            color: #666;
            cursor: not-allowed;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            background-color: #000000;
            color: #ffffff;
            padding: 12px 0;
            text-align: center;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
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

    <div class="split-card">
        <div class="form-pane">
            <h2 class="form-header">Update Status</h2>

            <?php if ($success_msg): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>
            
            <?php if ($error_msg): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form method="POST" action="" id="updateForm">
                <div class="form-row">
                    <label for="customer_id">Customer Id</label>
                    <select name="customer_id" id="customer_id" required>
                        <option value="" disabled selected>Select ID</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?php echo htmlspecialchars($c['id_number']); ?>">
                                <?php echo htmlspecialchars($c['id_number']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <label for="room_number">Room Number</label>
                    <input type="text" name="room_number" id="room_number" required>
                </div>

                <div class="form-row">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" required>
                </div>

                <div class="form-row">
                    <label for="checkin_time">Checkin Time</label>
                    <input type="text" name="checkin_time" id="checkin_time" readonly>
                </div>

                <div class="form-row">
                    <label for="deposit">Amount Paid</label>
                    <input type="number" step="0.01" name="deposit" id="deposit" required>
                </div>

                <div class="form-row">
                    <label for="remaining_price">Pending Paid</label>
                    <input type="number" step="0.01" name="remaining_price" id="remaining_price" required>
                </div>

                <div class="button-group">
                    <button type="button" class="btn" onclick="fetchCustomerData()">Check</button>
                    <button type="submit" name="update" class="btn" id="updateBtn">Update</button>
                    <a href="reception.php" class="btn">Back</a>
                </div>
            </form>
        </div>

        <div class="image-pane">
        </div>
    </div>

    <script>
        const customersData = <?php echo json_encode($customers); ?>;

        function fetchCustomerData() {
            const select = document.getElementById('customer_id');
            const docId = select.value;
            
            if (!docId) {
                alert("Please select a Customer ID first.");
                return;
            }

            // Find customer details in the array
            const customer = customersData.find(c => c.id_number === docId);

            if (customer) {
                document.getElementById('room_number').value = customer.room_number;
                document.getElementById('name').value = customer.name;
                document.getElementById('checkin_time').value = customer.checkin_time;
                document.getElementById('deposit').value = customer.deposit;
                document.getElementById('remaining_price').value = customer.remaining_price;
            }
        }
        
        // Auto-fetch data automatically when dropdown changes to save time
        document.getElementById('customer_id').addEventListener('change', fetchCustomerData);
    </script>
</body>
</html>
