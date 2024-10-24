<?php
session_start();
include '../db.php'; // Include database connection

// Initialize variables
$message = "";

// Check if the admin is already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php"); // Redirect to the dashboard
    exit();
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check the admin credentials
    $query = "SELECT * FROM admin WHERE username = ?"; // Use prepared statements to avoid SQL injection
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username); // Bind the username parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();

        // Check if the password matches (assuming passwords are stored in plain text; if hashed, use password_verify)
        if ($password == $admin['password']) {
            // Password matches, set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username']; // Store username in session
            $_SESSION['admin_id'] = $admin['admin_id']; // Store admin ID in session
            header("Location: dashboard.php"); // Redirect to the admin dashboard
            exit();
        } else {
            $message = "Invalid username or password."; // Incorrect password
        }
    } else {
        $message = "Invalid username or password."; // Incorrect username
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Admin Login</title>
    <style>
        /* Styles remain the same */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }

        header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            position: absolute;
            top: 20px;
            left: 20px;
        }

        h1 {
            color: #333;
            margin: 0;
            margin-left: 10px;
            font-size: 24px;
        }

        main {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 93%;
            padding: 10px;
            margin: 5px 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 8px;
            border-radius: 5px;
            cursor: pointer;
            width: 65%;
            font-size: 14px;
            margin: 0 auto;
            display: block;
        }

        button:hover {
            background-color: #0056b3;
        }

        p {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 10px;
            text-decoration: none;
            color: #007BFF;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .logo {
            width: 80px;
            height: auto;
        }
    </style>
</head>
<body>
    <header>
        <img src="../assets/images/website/logo.jpg" alt="Logo" class="logo">
        <h1>Admin Login</h1>
    </header>
    <main>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (!empty($message)) : ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
        <a class="back-link" href="../index.php">Back</a>
    </main>
</body>
</html>
