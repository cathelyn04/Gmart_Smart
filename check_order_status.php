<?php
session_start();
require '../db.php'; // Ensure db.php is correctly configured

$message = '';
$order_status = '';

if (isset($_POST['check_status'])) {
    $order_id = $_POST['order_id'];

    // Fetch the order status based on the provided order ID
    $order_query = "SELECT order_status FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($order_query);
    if ($stmt) {
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $stmt->bind_result($order_status);
        $stmt->fetch();
        $stmt->close();

        if ($order_status) {
            $message = "The status for Order ID: $order_id is '$order_status'.";
        } else {
            $message = "No order found with Order ID: $order_id.";
        }
    } else {
        $message = "Error preparing statement: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Order Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
            color: #333;
        }
        h1 {
            color: #333;
        }
        .form-container {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .message {
            color: green;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid green;
            border-radius: 5px;
            background-color: #e8f5e9;
            display: <?php echo $message ? 'block' : 'none'; ?>; /* Show if message is set */
        }
    </style>
</head>
<body>
    <h1>Check Order Status</h1>

    <div class="message"><?php echo htmlspecialchars($message); ?></div>

    <div class="form-container">
        <form action="check_order_status.php" method="POST">
            <label for="order_id">Enter Order ID:</label>
            <input type="number" name="order_id" id="order_id" required>
            <button type="submit" name="check_status">Check Status</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">
            <a href="explore.php" style="text-decoration: underline; color: blue;">Back to Dashboard</a>
        </p>
    </div>
</body>
</html>
