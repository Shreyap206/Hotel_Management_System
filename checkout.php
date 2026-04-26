<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once 'db_connect.php';

$success_msg = '';
$error_msg = '';

// Fetch all customers for the dropdown
$customers = [];
try {
    $stmt = $conn->query("SELECT id_number, room_number, checkin_time FROM customers WHERE status = 'Living'");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Database error: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $doc_number = $_POST['customer_id'];
        $room_number = $_POST['room_number'];

        // Start transaction
        $conn->beginTransaction();

        // 1. Update customer status to Checkout instead of deleting
        $del_stmt = $conn->prepare("UPDATE customers SET status = 'Checkout' WHERE id_number = :doc_num");
        $del_stmt->bindParam(':doc_num', $doc_number);
        $del_stmt->execute();

        // 2. Update room to Available and Dirty
        $upd_stmt = $conn->prepare("UPDATE rooms SET availability = 'Available', status = 'Dirty' WHERE room_number = :room_num");
        $upd_stmt->bindParam(':room_num', $room_number);
        $upd_stmt->execute();

        $conn->commit();
        $success_msg = "Checkout successful! Room is now available for cleaning.";
        
        // Refresh customer list
        $stmt = $conn->query("SELECT id_number, room_number, checkin_time FROM customers WHERE status = 'Living'");
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $conn->rollBack();
        $error_msg = "Checkout failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 30px;
            color: #0b4b8a; /* Blue header matching screenshot */
        }

        .image-pane {
            width: 50%;
            background-image: url('assets/images/checkout.png');
            background-size: cover;
            background-position: center;
            background-color: #f8f9fa;
        }

        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .form-row label {
            width: 140px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #333;
        }

        .input-group {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-row select {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-row select:focus {
            border-color: #0b4b8a;
        }

        .tick-icon {
            color: #28a745; /* Green checkmark */
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: background-color 0.2s;
        }

        .tick-icon:hover {
            background-color: #e6f4ea;
        }

        .display-value {
            flex: 1;
            font-size: 0.95rem;
            font-weight: 500;
            color: #000;
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
            <h2 class="form-header">Checkout</h2>

            <?php if ($success_msg): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>
            
            <?php if ($error_msg): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form method="POST" action="" id="checkoutForm">
                <input type="hidden" name="room_number" id="hidden_room_number" value="">
                
                <div class="form-row">
                    <label for="customer_id">Customer id</label>
                    <div class="input-group">
                        <select name="customer_id" id="customer_id" required>
                            <option value="" disabled selected>Select ID</option>
                            <?php foreach ($customers as $c): ?>
                                <option value="<?php echo htmlspecialchars($c['id_number']); ?>">
                                    <?php echo htmlspecialchars($c['id_number']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="tick-icon" onclick="fetchCustomerData()" title="Fetch Data">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        </button>
                    </div>
                </div>

                <div class="form-row">
                    <label>Room Number</label>
                    <div class="display-value" id="disp_room_number">-</div>
                </div>

                <div class="form-row">
                    <label>Checkin Time</label>
                    <div class="display-value" id="disp_checkin">-</div>
                </div>

                <div class="form-row">
                    <label>Checkout Time</label>
                    <div class="display-value" id="disp_checkout">
                        <?php echo date('D M d H:i:s T Y'); ?>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="submit-btn" id="checkoutBtn" disabled>Checkout</button>
                    <a href="reception.php" class="cancel-btn">Back</a>
                </div>
            </form>
        </div>

        <div class="image-pane">
        </div>
    </div>

    <!-- Pass PHP customer array to JS safely -->
    <script>
        const customersData = <?php echo json_encode($customers); ?>;

        function fetchCustomerData() {
            const select = document.getElementById('customer_id');
            const docId = select.value;
            
            if (!docId) return;

            // Find customer details in the array
            const customer = customersData.find(c => c.id_number === docId);

            if (customer) {
                document.getElementById('disp_room_number').innerText = customer.room_number;
                document.getElementById('hidden_room_number').value = customer.room_number;
                document.getElementById('disp_checkin').innerText = customer.checkin_time;
                
                // Enable checkout button
                document.getElementById('checkoutBtn').disabled = false;
            }
        }
        
        // Also fetch data automatically when dropdown changes
        document.getElementById('customer_id').addEventListener('change', fetchCustomerData);
    </script>
</body>
</html>
