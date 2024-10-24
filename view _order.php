<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include '../db.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch order details
    $query = "SELECT o.*, c.username 
              FROM orders o 
              JOIN customer c ON o.customer_id = c.customer_id 
              WHERE o.order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    // Fetch order items (if applicable)
    $item_query = "SELECT oi.*, p.product_name 
                   FROM order_items oi 
                   JOIN products p ON oi.product_id = p.product_id 
                   WHERE oi.order_id = ?";
    $item_stmt = $conn->prepare($item_query);
    $item_stmt->bind_param('i', $order_id);
    $item_stmt->execute();
    $items = $item_stmt->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
</head>
<body>
    <h1>Order Details</h1>
    <p>Order ID: <?php echo $order['order_id']; ?></p>
    <p>Customer Username: <?php echo $order['username']; ?></p>
    <p>Order Date: <?php echo $order['order_date']; ?></p>
    <p>Total Amount: RM <?php echo number_format($order['total_amount'], 2); ?></p>
    <p>Order Status: <?php echo $order['order_status']; ?></p>

    <h2>Items in this Order</h2>
    <ul>
        <?php while ($item = $items->fetch_assoc()): ?>
            <li><?php echo $item['product_name']; ?> - Quantity: <?php echo $item['quantity']; ?> - Price: RM <?php echo number_format($item['price'], 2); ?></li>
        <?php endwhile; ?>
    </ul>

    <a href="manage_orders.php">Back to Orders</a>
</body>
</html>
