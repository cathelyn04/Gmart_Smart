<?php
session_start();
require '../db.php'; // Ensure a valid db.php file for database connection

if (!isset($_SESSION['customer_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get the customer ID from the session
$customer_id = $_SESSION['user_id']; // Assuming this is still the user ID

// Calculate the total price
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

// Get the total price
$total_price = calculateTotal($_SESSION['cart'], $conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into orders table
        $order_query = "INSERT INTO orders (customer_id, total_amount, order_status) VALUES (?, ?, 'Pending')";
        $stmt = $conn->prepare($order_query);
        $stmt->bind_param("id", $customer_id, $total_price); // "id" stands for integer and decimal
        $stmt->execute();
        $order_id = $stmt->insert_id; // Get the generated order_id

        // Insert into order_items table
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product_query = "SELECT price FROM products WHERE product_id = ?";
            $stmt = $conn->prepare($product_query);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();

            // Insert each item into order_items
            $item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($item_query);
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $product['price']);
            $stmt->execute();
        }

        // Insert into order_status table (initial status 'Pending')
        $status_query = "INSERT INTO order_status (order_id, status) VALUES (?, 'Pending')";
        $stmt = $conn->prepare($status_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        // Clear the cart after successful order
        unset($_SESSION['cart']);

        // Redirect to a success page or order confirmation page
        header("Location: order_success.php?order_id=" . $order_id);
        exit();

    } catch (Exception $e) {
        // Rollback transaction on failure
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: checkout.php"); // If accessed directly, redirect back to checkout
    exit();
}
?>
