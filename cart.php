<?php
session_start();
include '../db.php';

// Check if the customer is logged in
if (!isset($_SESSION['customer_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get user ID from session (make sure it's stored during login)
$user_id = $_SESSION['user_id'];

// Retrieve cart items for the logged-in user
$cart_items = [];
$total_price = 0;

// Fetch items from the cart
$query = "SELECT c.quantity, p.product_name, p.price 
          FROM cart c 
          JOIN products p ON c.product_id = p.product_id 
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['quantity'] * $row['price'];
}

// Check if the cart is empty
if (empty($cart_items)) {
    echo "<p>Your shopping cart is empty.</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
</head>
<body>
    <h1>Your Shopping Cart</h1>
    
    <!-- Display cart items -->
    <?php if (!empty($cart_items)): ?>
        <table border="1">
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price (RM)</th>
                <th>Total (RM)</th>
            </tr>
            <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity'] * $item['price']); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3">Total</td>
                <td>RM <?php echo number_format($total_price, 2); ?></td>
            </tr>
        </table>
        
        <!-- Place Order Button -->
        <form action="place_order.php" method="POST">
            <button type="submit" name="place_order">Place Order</button>
        </form>
    <?php endif; ?>
    
    <br>
    <a href="customer_dashboard.php">Back to Dashboard</a>
</body>
</html>
