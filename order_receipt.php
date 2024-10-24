<?php
session_start();
require '../db.php';

// Ensure the user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "User not logged in.";
    exit;
}

// Get the order_id from the URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$customer_id = $_SESSION['customer_id'];

// Debugging: Check if order_id and customer_id are valid
echo "Customer ID: " . $customer_id . "<br>";
echo "Order ID: " . $order_id . "<br>";

// Fetch order details from the database
$sql_order = "
    SELECT o.order_id, o.order_date, od.order_total, os.status_name AS order_status
    FROM orders o
    JOIN order_details od ON o.order_id = od.order_id
    JOIN order_status os ON od.order_status = os.status_id
    WHERE o.customer_id = ? AND o.order_id = ?
";
$stmt = $conn->prepare($sql_order);
$stmt->bind_param("ii", $customer_id, $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// Check if an order was found
if ($order) {
    // Fetch the items for this order
    $sql_items = "
        SELECT p.product_name, oi.quantity, oi.item_total
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        JOIN order_details od ON oi.order_detail_id = od.order_detail_id
        WHERE od.order_id = ?
    ";
    $stmt_items = $conn->prepare($sql_items);
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $items_result = $stmt_items->get_result();
} else {
    // No order found
    echo "<p>No order found for this ID.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <h2>Order Receipt</h2>

    <p>Thank you for your order! Your order has been placed successfully.</p>
    <p>Please wait for the status on the <strong>Order Details</strong> page to change to <strong>Ready to Pickup</strong>.</p>
    <p>Payment will be made at the counter during pickup.</p>

    <h3>Order Summary</h3>
    <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
    <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
    <p><strong>Order Status:</strong> <?= htmlspecialchars($order['order_status']) ?></p>

    <h3>Items Ordered</h3>
    <table>
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Total Price (RM)</th>
        </tr>
        <?php while ($item = $items_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= htmlspecialchars($item['quantity']) ?></td>
                <td>RM <?= number_format($item['item_total'], 2) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h3>Total Amount</h3>
    <p><strong>RM <?= number_format($order['order_total'], 2) ?></strong></p>

    <p><a href="order_details.php?order_id=<?= $order_id ?>">View Order Details</a></p>
</body>
</html>

<?php
$stmt->close();
$stmt_items->close();
$conn->close();
?>
