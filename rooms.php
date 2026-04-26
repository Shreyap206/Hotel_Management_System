<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once 'db_connect.php';

$rooms = [];
try {
    // Fetch distinct bed types for the filter
    $bt_stmt = $conn->query("SELECT DISTINCT bed_type FROM rooms ORDER BY bed_type");
    $bed_types = $bt_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all rooms
    $stmt = $conn->query("SELECT * FROM rooms");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
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

        .rooms-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            display: flex;
            width: 85%;
            max-width: 1200px;
            height: 650px;
            overflow: hidden;
        }

        .left-column {
            width: 50%;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: auto;
        }

        th, td {
            text-align: left;
            padding: 12px 10px;
            font-size: 0.9rem;
            border-bottom: 1px solid #eee;
        }

        th {
            font-weight: 700;
            color: #000;
        }

        td {
            color: #333;
        }

        .back-btn-container {
            margin-top: 30px;
            text-align: center;
        }

        .back-btn {
            background-color: #000000;
            color: #ffffff;
            padding: 12px 40px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 2px;
            transition: background-color 0.2s, transform 0.1s;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        .back-btn:hover {
            background-color: #333333;
            transform: scale(1.02);
        }

        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #eee;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .filter-group label {
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
        }

        .filter-group select {
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-group label {
            font-weight: 500;
            color: #333;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .right-column {
            width: 50%;
            background-image: url('assets/images/room_interior.png');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body>

    <h1 class="header-title">THE TAJ GROUP WELCOME TO</h1>

    <div class="rooms-card">
        <div class="left-column">
            
            <div class="filter-bar">
                <div class="filter-group">
                    <label for="bedTypeFilter">Bed Type</label>
                    <select id="bedTypeFilter">
                        <option value="All">All</option>
                        <?php foreach($bed_types as $bt): ?>
                            <option value="<?php echo htmlspecialchars($bt['bed_type']); ?>">
                                <?php echo htmlspecialchars($bt['bed_type']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="availableFilter">
                    <label for="availableFilter">Only display Available</label>
                </div>
            </div>

            <table id="roomsTable">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Availability</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Bed Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rooms)): ?>
                        <?php foreach ($rooms as $room): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                                <td><?php echo htmlspecialchars($room['availability']); ?></td>
                                <td><?php echo htmlspecialchars($room['status']); ?></td>
                                <td><?php echo htmlspecialchars($room['price']); ?></td>
                                <td><?php echo htmlspecialchars($room['bed_type']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No rooms found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="back-btn-container">
                <a href="reception.php" class="back-btn">Back</a>
            </div>
        </div>

        <div class="right-column">
        </div>
    </div>

    <script>
        const bedTypeFilter = document.getElementById('bedTypeFilter');
        const availableFilter = document.getElementById('availableFilter');
        const tableBody = document.querySelector('#roomsTable tbody');
        const rows = tableBody.querySelectorAll('tr');

        function applyFilters() {
            const selectedBedType = bedTypeFilter.value;
            const onlyAvailable = availableFilter.checked;

            rows.forEach(row => {
                // Skip the "No rooms found" row
                if (row.cells.length === 1) return;

                const availability = row.cells[1].innerText.trim();
                const bedType = row.cells[4].innerText.trim();

                let showRow = true;

                if (selectedBedType !== 'All' && bedType !== selectedBedType) {
                    showRow = false;
                }

                if (onlyAvailable && availability !== 'Available') {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            });
        }

        bedTypeFilter.addEventListener('change', applyFilters);
        availableFilter.addEventListener('change', applyFilters);
    </script>
</body>
</html>
