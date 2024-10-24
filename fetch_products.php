<?php
include '../db.php';

$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare the query for products
$search_query = $conn->real_escape_string($search_query);

if ($search_query === '') {
    // If no search query is provided, fetch all products
    $products_query = "SELECT * FROM products";
} else {
    // If a search query is provided, filter products by name
    $products_query = "SELECT * FROM products WHERE name LIKE '%$search_query%'";
}

$products_result = $conn->query($products_query);

// Display the products
if ($products_result->num_rows > 0) {
    while ($row = $products_result->fetch_assoc()) {
        echo "<li>";
        // Display product image
        echo "<img src='" . $row['image'] . "' alt='" . $row['product_name'] . "'>";
        echo "<div class='product-details'>";
        // Display product name and price
        echo "<strong>" . $row['product_name'] . "</strong> - RM" . $row['price'];
        echo "</div>";
        echo "<a href='add_to_cart.php?id=" . $row['product_id'] . "' class='add-to-cart'>Add to Cart</a>";
        echo "</li>";
    }
} else {
    echo "<p>No products found.</p>";
}
?>
