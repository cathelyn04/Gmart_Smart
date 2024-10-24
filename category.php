<?php
session_start();
include '../db.php';

if (!isset($_SESSION['customer_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get category ID from the query string
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Get category details
$category_result = $conn->query("SELECT * FROM categories WHERE category_id = $category_id");
$category = $category_result->fetch_assoc();

// Fetch products for the category
$products_result = $conn->query("SELECT * FROM products WHERE category_id = $category_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['category_name']); ?> - GMART SMART</title>
    <style>
        /* Add your CSS styles here */
        /* Reuse styles from the previous page as needed */
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($category['category_name']); ?></h1>
        <ul>
            <?php while ($product = $products_result->fetch_assoc()) : ?>
                <li>
                    <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="product-details">
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p>Price: RM<?php echo htmlspecialchars($product['price']); ?></p>
                    </div>
                    <a href="add_to_cart.php?id=<?php echo $product['product_id']; ?>" class="add-to-cart">Add to Cart</a>
                </li>
            <?php endwhile; ?>
        </ul>
        <a href="index.php" class="back-button">Back to Categories</a>
    </div>
</body>
</html>
