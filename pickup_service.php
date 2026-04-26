<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once 'db_connect.php';

$error_msg = '';
$drivers = [];
$car_types = [];
$selected_type = '';

try {
    // Fetch unique car companies for the dropdown
    $type_stmt = $conn->query("SELECT DISTINCT car_company FROM drivers ORDER BY car_company");
    $car_types = $type_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filter drivers if a type was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['car_type']) && !empty($_POST['car_type'])) {
        $selected_type = $_POST['car_type'];
        $stmt = $conn->prepare("SELECT * FROM drivers WHERE car_company = :car_type ORDER BY name");
        $stmt->bindParam(':car_type', $selected_type);
        $stmt->execute();
        $drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Fetch all drivers initially
        $stmt = $conn->query("SELECT * FROM drivers ORDER BY name");
        $drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error_msg = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickup Service</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .info-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 95%;
            max-width: 1200px;
            height: 600px;
            padding: 40px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }

        .page-header {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }

        .filter-section {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            gap: 15px;
        }

        .filter-section label {
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .filter-section select {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.95rem;
            outline: none;
            width: 250px;
            transition: border-color 0.2s;
        }

        .filter-section select:focus {
            border-color: #0b4b8a;
        }

        .table-container {
            flex-grow: 1;
            overflow-y: auto;
            overflow-x: auto;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }

        th, td {
            text-align: left;
            padding: 15px;
            font-size: 0.95rem;
            border-bottom: 1px solid #eee;
        }

        th {
            font-weight: 700;
            color: #000;
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 1;
        }

        td {
            color: #333;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: auto;
            padding-top: 20px;
        }

        .btn {
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
            transition: background-color 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background-color: #333333;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-weight: 500;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="info-card">
        <h2 class="page-header">Pickup Service</h2>

        <?php if ($error_msg): ?>
            <div class="alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="filter-section">
                <label for="car_type">Type of Car</label>
                <select name="car_type" id="car_type">
                    <option value="">All Companies</option>
                    <?php foreach ($car_types as $type): ?>
                        <option value="<?php echo htmlspecialchars($type['car_company']); ?>" 
                            <?php echo ($selected_type === $type['car_company']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type['car_company']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <!-- Submit button placed at bottom to match screenshot -->
            </div>

            <div class="table-container" id="printable-area">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Company</th>
                            <th>Available</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($drivers)): ?>
                            <?php foreach ($drivers as $d): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($d['name']); ?></td>
                                    <td><?php echo htmlspecialchars($d['age']); ?></td>
                                    <td><?php echo htmlspecialchars($d['gender']); ?></td>
                                    <td><?php echo htmlspecialchars($d['car_company']); ?></td>
                                    <td>
                                        <span style="color: <?php echo ($d['available'] == 'Yes') ? '#5cb85c' : '#d9534f'; ?>; font-weight: 600;">
                                            <?php echo htmlspecialchars($d['available']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($d['location']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No drivers found for this car company.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="button-group">
                <button type="submit" class="btn">Submit</button>
                <a href="reception.php" class="btn">Back</a>
            </div>
        </form>
    </div>

</body>
</html>
