<?php
// Include the database connection
require_once 'db_connect.php';

// At this point, $conn is available if the connection was successful.
// You can use it to fetch hotel details dynamically later.
$hotel_name = "THE GRAND RESORT"; // Changed name as requested
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $hotel_name; ?> - Management System</title>
    <!-- Google Fonts for modern typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <section class="hero-section">
        <!-- Header / Navigation Bar -->
        <header>
            <div class="logo-container">
                <a href="reception.php" class="logo-primary" style="text-decoration: none;">HOTEL MANAGEMENT</a>
                <div class="dropdown">
                    <a href="#" class="logo-secondary" id="adminDropdownBtn" style="display: flex; align-items: center;">
                        ADMIN
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 5px;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </a>
                    <div class="dropdown-content" id="adminDropdownContent">
                        <a href="add_room.php">Add Room</a>
                        <a href="add_employee.php">Add Employee</a>
                        <a href="add_driver.php">Add Driver</a>
                    </div>
                </div>
            </div>
            
            <!-- Optional Login Button on the right for better UX -->
            <div class="nav-links">
                <a href="login.php" class="login-btn">Login Admin</a>
            </div>
        </header>

        <!-- Main centered content -->
        <main class="main-content">
            <!-- Welcome text, similar layout to the provided image -->
            <h1 class="main-title">WELCOME TO <?php echo $hotel_name; ?></h1>
            
            <!-- Database error message hidden as requested -->
        </main>
    </section>

    </section>

    <script>
        document.getElementById('adminDropdownBtn').addEventListener('click', function(e) {
            e.preventDefault();
            const dropdownContent = document.getElementById('adminDropdownContent');
            dropdownContent.classList.toggle('show-dropdown');
        });

        // Close the dropdown if the user clicks outside of it
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                const dropdownContent = document.getElementById('adminDropdownContent');
                if (dropdownContent && dropdownContent.classList.contains('show-dropdown')) {
                    dropdownContent.classList.remove('show-dropdown');
                }
            }
        });
    </script>
</body>
</html>
