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
        $job = $_POST['job'];
        $salary = $_POST['salary'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $aadhar = $_POST['aadhar'];

        $sql = "INSERT INTO employees (name, age, gender, job, salary, phone, email, aadhar) 
                VALUES (:name, :age, :gender, :job, :salary, :phone, :email, :aadhar)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':job', $job);
        $stmt->bindParam(':salary', $salary);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':aadhar', $aadhar);

        if ($stmt->execute()) {
            $success_msg = "Employee record added successfully!";
        } else {
            $error_msg = "Failed to add employee. Please try again.";
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
    <title>Add Employee</title>
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
            max-width: 1100px;
            height: 650px;
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

        .image-pane {
            width: 50%;
            background-image: url('assets/images/hotel_staff.png');
            background-size: cover;
            background-position: center;
            background-color: #f8f9fa; /* fallback */
        }

        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-row label {
            width: 120px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #333;
            text-transform: uppercase;
        }

        .form-row input[type="text"],
        .form-row input[type="number"],
        .form-row input[type="email"],
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
        .form-row input[type="email"]:focus,
        .form-row select:focus {
            border-color: #0b4b8a;
        }

        .radio-group {
            flex: 1;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .radio-group label {
            width: auto;
            text-transform: none;
            font-weight: normal;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .submit-btn {
            background-color: #000000;
            color: #ffffff;
            padding: 12px 0;
            width: 150px;
            text-align: center;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            margin-left: 120px; /* align with inputs */
            transition: background-color 0.2s;
        }

        .submit-btn:hover {
            background-color: #333333;
        }

        .back-btn-container {
            margin-top: 20px;
            margin-left: 120px;
        }

        .back-btn {
            color: #0b4b8a;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .back-btn:hover {
            text-decoration: underline;
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
            <?php if ($success_msg): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>
            
            <?php if ($error_msg): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-row">
                    <label for="name">NAME</label>
                    <input type="text" name="name" id="name" required>
                </div>

                <div class="form-row">
                    <label for="age">AGE</label>
                    <input type="number" name="age" id="age" required>
                </div>

                <div class="form-row">
                    <label>GENDER</label>
                    <div class="radio-group">
                        <label><input type="radio" name="gender" value="Male" required> Male</label>
                        <label><input type="radio" name="gender" value="Female" required> Female</label>
                    </div>
                </div>

                <div class="form-row">
                    <label for="job">JOB</label>
                    <select name="job" id="job" required>
                        <option value="Front Desk Clerks">Front Desk Clerks</option>
                        <option value="Porters">Porters</option>
                        <option value="Housekeeping">Housekeeping</option>
                        <option value="Kitchen Staff">Kitchen Staff</option>
                        <option value="Room Service">Room Service</option>
                        <option value="Manager">Manager</option>
                        <option value="Accountant">Accountant</option>
                    </select>
                </div>

                <div class="form-row">
                    <label for="salary">SALARY</label>
                    <input type="number" step="0.01" name="salary" id="salary" required>
                </div>

                <div class="form-row">
                    <label for="phone">PHONE</label>
                    <input type="text" name="phone" id="phone" required>
                </div>

                <div class="form-row">
                    <label for="email">EMAIL</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="form-row">
                    <label for="aadhar">AADHAR</label>
                    <input type="text" name="aadhar" id="aadhar" required>
                </div>

                <button type="submit" class="submit-btn">SUBMIT</button>
                
                <div class="back-btn-container">
                    <a href="index.php" class="back-btn">← Back to Homepage</a>
                </div>
            </form>
        </div>

        <div class="image-pane">
        </div>
    </div>

</body>
</html>
