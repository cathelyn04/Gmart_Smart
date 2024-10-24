<?php
session_start();
require '../db.php'; // Ensure you have a valid db.php file for database connection

// Check if order_id is set in the URL
if (!isset($_GET['order_id'])) {
    header("Location: customer_dashboard.php"); // Redirect to customer dashboard if no order_id is provided
    exit();
}

// Get the order ID from the URL
$order_id = $_GET['order_id'];

// Fetch order details
$order_query = "SELECT o.order_id, o.total_amount, o.order_date, os.status
                FROM orders o
                JOIN order_status os ON o.order_id = os.order_id
                WHERE o.order_id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows > 0) {
    $order = $order_result->fetch_assoc();
} else {
    echo "Order not found.";
    exit();
}

// Fetch the ordered items
$items_query = "SELECT oi.quantity, oi.price, p.product_name
                FROM order_items oi
                JOIN products p ON oi.product_id = p.product_id
                WHERE oi.order_id = ?";
$stmt = $conn->prepare($items_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Confirmation</h1>
        <p>Thank you for your order!</p>
        <p style="color: blue;">Please wait for your orders to "Ready to Pickup" status!</p>
        
        <div class="alert alert-info">
            <strong>Notice:</strong> Please pay at the counter when you pick up your order. Show your receipt to the staff for processing.
        </div>

        <h2>Order Details</h2>
        <p>Order ID: <?php echo htmlspecialchars($order['order_id']); ?></p>
        <p>Order Date: <?php echo htmlspecialchars($order['order_date']); ?></p>
        <p>Order Status: <?php echo htmlspecialchars($order['status']); ?></p>

        <h2>Items Ordered</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>RM <?php echo htmlspecialchars(number_format($item['price'], 2)); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <p class="total">Total Amount: RM <?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></p>

        <a href="explore.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>
