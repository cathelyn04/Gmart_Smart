<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include '../db.php';

$error_message = ""; // Initialize an error message variable
$success_message = ""; // Initialize a success message variable

// Check if delete_id is set for product deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM products WHERE product_id = $delete_id";
    if ($conn->query($delete_query)) {
        $success_message = "Product deleted successfully";
    } else {
        $error_message = "Error deleting product: " . $conn->error;
    }
}

// Check if we are editing a product
$product_id = '';
$name = '';
$price = '';
$category_id = '';
$stock = '';
$image = '';

if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $edit_query = "SELECT * FROM products WHERE id = $edit_id";
    $edit_result = $conn->query($edit_query);
    if ($edit_result->num_rows > 0) {
        $row = $edit_result->fetch_assoc();
        $product_id = $row['id'];
        $name = $row['name'];
        $price = $row['price'];
        $category_id = $row['category_id'];
        $stock = $row['stock'];
        $image = $row['image'];
    }
}

// Handle form submission for adding or editing a product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = mysqli_real_escape_string($conn, $_POST['name']); // Escape special characters
    $price = $_POST['price'];
    $category_id = $_POST['category'];
    $quantity_in_stock = $_POST['stock'];
    $product_id = $_POST['product_id'];

    // Check if the product already exists
    $check_query = "SELECT * FROM products WHERE product_name = '$product_name' AND product_id != '$product_id'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        $error_message = "Product already exists. Please choose a different name."; // Set the error message
    } else {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($_FILES['image']['name']);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Allow only certain file formats
            $allowed_types = array("jpg", "png", "jpeg", "gif");
            if (in_array($imageFileType, $allowed_types)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image = $target_file;
                } else {
                    $error_message = "Sorry, there was an error uploading your file.";
                }
            } else {
                $error_message = "Only JPG, JPEG, PNG & GIF files are allowed.";
            }
        }

        // Insert or Update product in the database
        if ($product_id) {
            // Update existing product
            $update_query = "UPDATE products SET name='$product_name', price='$price', category_id='$category_id', stock='$quantity_in_stock', image='$image' WHERE id='$product_id'";
            if ($conn->query($update_query)) {
                $success_message = "Product updated successfully";
            } else {
                $error_message = "Error updating product: " . $conn->error;
            }
        } else {
            // Add new product
            $insert_query = "INSERT INTO products (product_name, price, category_id, quantity_in_stock, image) VALUES ('$product_name', '$price', '$category_id', '$quantity_in_stock', '$image')";
            if ($conn->query($insert_query)) {
                $success_message = "Product added successfully";

                // Redirect to the same page to avoid form resubmission
                header("Location: manage_products.php"); // Ensure this matches your filename
                exit();
            } else {
                $error_message = "Error adding product: " . $conn->error;
            }
        }
    }
}

// Check stock adjustment
if (isset($_GET['action']) && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $action = $_GET['action'];

    // Update stock based on action
    if ($action === 'increase') {
        $update_stock_query = "UPDATE products SET stock = stock + 1 WHERE id = $product_id";
    } elseif ($action === 'decrease') {
        $update_stock_query = "UPDATE products SET stock = stock - 1 WHERE id = $product_id AND stock > 0"; // Prevent stock from going negative
    }

    if ($conn->query($update_stock_query)) {
        $success_message = "Stock updated successfully";
    } else {
        $error_message = "Error updating stock: " . $conn->error;
    }
}

// Fetch products for display
$products_query = "SELECT p.product_id, p.product_name, p.price, p.quantity_in_stock, c.category_name AS category, p.image FROM products p JOIN categories c ON p.category_id = c.category_id";
$products_result = $conn->query($products_query);

// Fetch categories for the dropdown
$categories = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        input[type="text"], input[type="number"], input[type="file"], select {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }
        
        #error-message, #success-message {
            display: none; /* Initially hide the message */
            color: red; /* Change the color to red for visibility */
            position: fixed; /* Make it fixed at the bottom */
            bottom: 10px; /* Position it 10px from the bottom */
            left: 50%; /* Center it horizontally */
            transform: translateX(-50%); /* Center adjustment */
            z-index: 1000; /* Ensure it's above other content */
        }

        #success-message {
            color: green; /* Change the color to green for success */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
    <script>
        function showMessage(messageType) {
            var message = document.getElementById(messageType);
            message.style.display = "block"; // Show the message
            setTimeout(function() {
                message.style.display = "none"; // Hide it after 5 seconds
            }, 5000);
        }
    </script>
</head>
<body>
    <h1>Manage Products</h1>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>"> <!-- Hidden field for editing -->
        <input type="text" name="name" placeholder="Product Name" required value="<?php echo $name; ?>">
        <input type="number" name="price" placeholder="Price (RM)" required step="0.01" min="0" value="<?php echo $price; ?>">
        <select name="category" required>
            <option value="">Select Category</option>
            <?php while ($row = $categories->fetch_assoc()) : ?>
                <option value="<?php echo $row['category_id']; ?>" <?php if ($row['category_id'] == $category_id) echo 'selected'; ?>>
                    <?php echo $row['category_name']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <input type="number" name="stock" placeholder="Stock Quantity" required min="0" value="<?php echo $stock; ?>">
        <input type="file" name="image" accept="image/*">
        <?php if ($image): ?>
            <img src="<?php echo $image; ?>" alt="Product Image" width="100"><br>
            <span>Current Image</span>
        <?php endif; ?>
        <button type="submit">Save Product</button>
    </form>

    <p>
        <a href="dashboard.php">Back to Dashboard</a>
    </p>

    <div id="error-message"><?php if ($error_message) echo $error_message; ?></div>
    <div id="success-message"><?php if ($success_message) echo $success_message; ?></div>

    <script>
        // Show messages if they exist
        <?php if ($error_message) echo 'showMessage("error-message");'; ?>
        <?php if ($success_message) echo 'showMessage("success-message");'; ?>
    </script>


    <h2>Product List</h2>
    <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
    <tr></tr>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price (RM)</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($products_result->num_rows > 0): ?>
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $product['product_name']; ?></td>
                        <td><?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo $product['category']; ?></td>
                        <td><?php echo $product['quantity_in_stock']; ?></td>
                        <td><img src="<?php echo $product['image']; ?>" alt="Product Image" width="50"></td>
                        <td>
                            <a href="manage_products.php?delete_id=<?php echo $product['product_id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a> |
                            <a href="manage_products.php?action=increase&product_id=<?php echo $product['product_id']; ?>"> + </a> | 
                            <a href="manage_products.php?action=decrease&product_id=<?php echo $product['product_id']; ?>"> - </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No products found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        </table>
    </table>
</body>
</html>
