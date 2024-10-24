<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include '../db.php';

if (isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $stock = $_POST['stock'];

    // Handle image upload (if the admin uploaded a new image)
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target_dir = "../assets/images/";
        $target_file = $target_dir . basename($image);

        // Move the uploaded file to the assets/images directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Update the product with a new image
            $query = "UPDATE products SET name='$name', price='$price', category_id='$category_id', stock='$stock', image='$image' WHERE id='$product_id'";
        } else {
            echo "Error uploading image.";
            exit();
        }
    } else {
        // Update without changing the image
        $query = "UPDATE products SET name='$name', price='$price', category_id='$category_id', stock='$stock' WHERE id='$product_id'";
    }

    if ($conn->query($query) === TRUE) {
        header("Location: manage_products.php?update_success=1");
        exit();
    } else {
        echo "Error updating product: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
