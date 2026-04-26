<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once 'db_connect.php';

$managers = [];
try {
    // Only fetch employees where job is 'Manager'
    $stmt = $conn->query("SELECT * FROM employees WHERE job = 'Manager' ORDER BY created_at DESC");
    $managers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Info</title>
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

        .header-title {
            color: #ffffff;
            font-size: 2.5rem;
            font-weight: 400;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
        }

        .info-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 95%;
            max-width: 1200px;
            height: 600px;
            padding: 30px 40px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .action-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-wrapper {
            position: relative;
            width: 100%;
            max-width: 400px;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            width: 18px;
            height: 18px;
            pointer-events: none;
        }

        .search-bar {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 1px solid #e0e0e0;
            border-radius: 30px;
            font-size: 0.95rem;
            outline: none;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
            color: #333;
        }

        .search-bar::placeholder {
            color: #aaa;
        }

        .search-bar:focus {
            border-color: #000000;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(0,0,0,0.1);
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
            padding: 12px 15px;
            font-size: 0.9rem;
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

        .back-btn-container {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
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
    </style>
</head>
<body>

    <h1 class="header-title">THE TAJ GROUP WELCOME TO</h1>

    <div class="info-card">
        <div class="action-container">
            <div class="search-wrapper">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="searchInput" class="search-bar" placeholder="Search managers by name, job, or email...">
            </div>
        </div>
        
        <div class="table-container" id="printable-area">
            <table id="managerTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Job</th>
                        <th>Salary</th>
                        <th>Phone No</th>
                        <th>Email Id</th>
                        <th>Aadhar No</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($managers)): ?>
                        <?php foreach ($managers as $mgr): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mgr['name']); ?></td>
                                <td><?php echo htmlspecialchars($mgr['age']); ?></td>
                                <td><?php echo htmlspecialchars($mgr['gender']); ?></td>
                                <td><?php echo htmlspecialchars($mgr['job']); ?></td>
                                <td>$<?php echo number_format($mgr['salary'], 2); ?></td>
                                <td><?php echo htmlspecialchars($mgr['phone']); ?></td>
                                <td><?php echo htmlspecialchars($mgr['email']); ?></td>
                                <td><?php echo htmlspecialchars($mgr['aadhar']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">No manager records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="back-btn-container">
            <a href="reception.php" class="back-btn">Back</a>
            <button onclick="printPDF()" class="back-btn" style="margin-left: 15px; background-color: #4CAF50;">Print to PDF</button>
        </div>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#managerTable tbody tr');
            
            rows.forEach(row => {
                if (row.cells.length === 1) return; // skip 'no records' row
                let text = row.innerText.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // PDF Print functionality
        function printPDF() {
            const element = document.getElementById('printable-area');
            const opt = {
                margin:       0.5,
                filename:     'Manager_List.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }
            };
            
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
