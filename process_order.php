<?php
session_start();
require '../db.php'; // Ensure the path is correct relative to the location of this file

if (!isset($_SESSION['customer_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Check if the cart is empty
if (empty($_SESSION['cart'])) {
    // Redirect to cart page if the cart is empty
    header("Location: review_cart.php");
    exit();
}

// Process the order
$cart = $_SESSION['cart'];
$user_id = $_SESSION['user_id']; // Assuming you are using session to store user ID

// Initialize total amount
$total_amount = 0;

// Calculate the total amount from the cart
foreach ($cart as $item) {
    // Ensure that $item is an array with price and quantity keys
    if (is_array($item) && isset($item['price'], $item['quantity'])) {
        $total_amount += $item['price'] * $item['quantity'];
    }
}

// Get the current date and time
$order_date = date('Y-m-d H:i:s'); // Current date and time
$order_status = 'Pending'; // Default order status

// Check if the database connection is established
if ($db) { // Check if $db is successfully created
    // Verify that the user_id exists in the customer table
    $stmtCheck = $db->prepare("SELECT COUNT(*) FROM customer WHERE customer_id = ?");
    $stmtCheck->bind_param("i", $user_id);
    $stmtCheck->execute();
    $stmtCheck->bind_result($count);
    $stmtCheck->fetch();
    $stmtCheck->close();

    if ($count == 0) {
        echo "Error: The user ID does not exist in the customer table.";
        exit();
    }

    // Prepare the SQL statement to insert the order
    $stmt = $db->prepare("INSERT INTO orders (user_id, order_date, total_amount, order_status) VALUES (?, ?, ?, ?)");
    
    // Bind parameters to the statement
    $stmt->bind_param("isss", $user_id, $order_date, $total_amount, $order_status);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Clear the cart after placing the order
        $_SESSION['cart'] = [];

        // Redirect to order confirmation page
        header("Location: order_confirmation.php"); // Change this to your actual confirmation page
        exit();
    } else {
        // Handle error (for example, log it or display an error message)
        echo "Error placing the order: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Database connection not established."; // Will only trigger if $db is not created correctly
}
?>
