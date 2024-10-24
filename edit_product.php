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

    // Fetch the product data
    $query = "SELECT * FROM products WHERE product_id = $product_id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    } else {
        echo "Product not found.";
        exit();
    }
} else {
    echo "No product ID specified.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $quantity_in_stock = $_POST['quantity_in_stock']; // Add this line to get the quantity

    // Update query
    $update_query = "UPDATE products SET product_name = '$product_name', price = '$price', category_id = '$category_id', quantity_in_stock = '$quantity_in_stock' WHERE product_id = $product_id";

    if (mysqli_query($conn, $update_query)) {
        header("Location: manage_products.php?success=Product updated successfully");
        exit();
    } else {
        echo "Failed to update product: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        .back-link {
            text-decoration: underline;
            color: blue;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h1>Edit Product</h1>
    <form method="POST">
        <label for="product_name">Product Name:</label>
        <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required><br>

        <label for="price">Price (RM):</label>
        <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required><br>

        <label for="quantity_in_stock">Quantity in Stock:</label>
        <input type="number" name="quantity_in_stock" value="<?php echo htmlspecialchars($product['quantity_in_stock']); ?>" required><br>

        <label for="category_id">Category:</label>
        <select name="category_id">
            <?php
            // Fetch categories for the dropdown
            $categories_query = mysqli_query($conn, "SELECT * FROM categories");
            while ($category = mysqli_fetch_assoc($categories_query)) {
                $selected = ($product['category_id'] == $category['category_id']) ? 'selected' : '';
                echo "<option value='{$category['category_id']}' $selected>{$category['category_name']}</option>";
            }
            ?>
        </select><br>

        <button type="submit">Update Product</button>
    </form>
    <a href="dashboard.php" class="back-link">Back to Dashboard</a>
</body>
</html>
