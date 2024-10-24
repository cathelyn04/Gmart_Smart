<?php
session_start();
require '../db.php'; // Correct path to db.php

// Initialize cart array if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if the product ID is set in the URL
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']); // Ensure the product ID is an integer

    // Check if the product already exists in the cart
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++; // Increase quantity if it exists
    } else {
        $_SESSION['cart'][$product_id] = 1; // Add new product with quantity 1
    }

    // Redirect to the cart or product page after adding
    header("Location: review_cart.php"); // Redirect to the review cart page
    exit();
} else {
    // If no product ID is provided, redirect to the homepage or product listing
    header("Location: explore_product.php"); // Adjust the location as necessary
    exit();
}
?>
