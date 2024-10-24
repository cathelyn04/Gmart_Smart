<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include('../db.php');

// Check if product ID is passed
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Delete query
    $delete_query = "DELETE FROM products WHERE product_id = $product_id";

    if (mysqli_query($conn, $delete_query)) {
        header("Location: manage_products.php?success=Product deleted successfully");
        exit();
    } else {
        echo "Failed to delete product: " . mysqli_error($conn);
    }
} else {
    echo "No product ID specified.";
    exit();
}
?>
