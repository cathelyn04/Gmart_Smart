<?php
session_start();
require '../db.php'; // Corrected path to db.php

// Initialize cart array if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Function to calculate total price
function calculateTotal($cart, $conn) {
    $total = 0;
    foreach ($cart as $product_id => $quantity) {
        $query = "SELECT price FROM products WHERE product_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $total += $row['price'] * $quantity;
        }
    }
    return $total;
}

// Calculate total
$total = calculateTotal($_SESSION['cart'], $conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        header {
            background-color: #f8f8f8;
            padding: 20px;
            text-align: center;
        }
        .header-content {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo {
            max-width: 50px;
            margin-right: 10px;
        }
        main {
            flex: 1;
            padding: 20px;
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
            font-size: 1.2em;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        footer {
            background-color: #f1f1f1;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <img src="../assets/images/website/logo.jpg" alt="Logo" class="logo">
            <h1>Checkout</h1>
        </div>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($_SESSION['cart'])): ?>
                    <tr>
                        <td colspan="3">Your cart is empty.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($_SESSION['cart'] as $product_id => $quantity): ?>
                        <?php
                        // Fetch product details
                        $query = "SELECT product_name, price FROM products WHERE product_id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("i", $product_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $product = $result->fetch_assoc();
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($quantity); ?></td>
                            <td>RM <?php echo htmlspecialchars(number_format($product['price'], 2)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="total">
            <h2>Total: RM <?php echo htmlspecialchars(number_format($total, 2)); ?></h2>
        </div>
        <form action="place_order.php" method="POST">
            <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total); ?>"> <!-- Add this hidden field -->
            <a href="review_cart.php" class="btn">Go Back to Cart</a>
            <button type="submit" class="btn">Place Order</button> <!-- Change link to button -->
        </form>
    </main>
    <footer>
        <p>&copy; 2024 Gmart Smart. All rights reserved.</p>
    </footer>
</body>
</html>
