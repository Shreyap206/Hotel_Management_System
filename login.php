<?php
session_start();
require_once 'db_connect.php';

$error = '';

// If already logged in, redirect to reception
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: reception.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Query to check user
        $sql = "SELECT id, username, password FROM admin_users WHERE username = ?";
        try {
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bindParam(1, $username, PDO::PARAM_STR);
                
                if ($stmt->execute()) {
                    if ($stmt->rowCount() == 1) {
                        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if (password_verify($password, $row['password'])) {
                                // Password is correct, start a new session
                                session_regenerate_id();
                                $_SESSION['admin_logged_in'] = true;
                                $_SESSION['admin_id'] = $row['id'];
                                $_SESSION['admin_username'] = $row['username'];
                                
                                header("Location: reception.php");
                                exit;
                            } else {
                                $error = "Invalid username or password.";
                            }
                        }
                    } else {
                        $error = "Invalid username or password.";
                    }
                } else {
                    $error = "Oops! Something went wrong. Please try again later.";
                }
            }
        } catch(PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - The Grand Resort</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            margin: auto;
            position: relative;
            z-index: 2;
            margin-top: 10vh;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
        }
        .form-group input:focus {
            border-color: #0b4b8a;
            outline: none;
            box-shadow: 0 0 5px rgba(11, 75, 138, 0.2);
        }
        .btn-submit {
            background-color: #0b4b8a;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            width: 100%;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-submit:hover {
            background-color: #083666;
        }
        .error-msg {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <section class="hero-section" style="justify-content: center; align-items: center;">
        <header>
            <div class="logo-container">
                <a href="index.php" class="logo-primary" style="text-decoration: none;">HOTEL MANAGEMENT</a>
            </div>
        </header>

        <div class="login-container">
            <div class="login-header">
                <h2>Admin Access</h2>
                <p style="color: #777;">Please enter your credentials to login.</p>
            </div>
            
            <?php if(!empty($error)): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-submit">Login</button>
            </form>
        </div>
    </section>
</body>
</html>
