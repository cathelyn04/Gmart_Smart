<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include('../db.php'); // Adjust the path as needed

// Fetch data from the database
$categories = mysqli_query($conn, "SELECT * FROM categories");
$customers = mysqli_query($conn, "SELECT * FROM customers");
$products = mysqli_query($conn, "SELECT * FROM products");

// Check for query errors
if (!$categories || !$customers || !$products) {
    die("Database query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007BFF;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .menu-icon {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
        }

        .menu-icon:hover {
            background-color: #0056b3;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            transition: background-color 0.3s;
        }

        .navbar a:hover {
            background-color: #0056b3;
            border-radius: 5px;
            padding: 10px 15px;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #ffffff;
            min-width: 200px;
            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 5px;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        footer {
            text-align: center;
            margin-top: 40px;
            color: #666;
        }

        .container {
            margin-top: 30px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }

       .table th {
            background-color: #007BFF;
            color: white;
        }

        .edit-button, .delete-button {
            padding: 8px 12px;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        .edit-button {
            background-color: #28a745;
        }

        .edit-button:hover {
            background-color: #218838;
        }

        .delete-button {
            background-color: #dc3545;
        }

        .delete-button:hover {
            background-color: #c82333;
        }

        .table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table tr:hover {
            background-color: #e0e0e0;
        }

    </style>
</head>
<body>
    <h1>Welcome to Admin Dashboard</h1>
    <div class="navbar">
        <div class="dropdown">
            <button class="menu-icon">&#9776; Manage</button>
            <div class="dropdown-content">
                <a href="manage_categories.php">Manage Categories</a>
                <a href="manage_products.php">Manage Products</a>
                <a href="manage_orders.php">Manage Orders</a>
                <a href="manage_users.php">Manage Users</a>
            </div>
        </div>
        <div>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Product Categories</h2>
        <table class="table">
            <tr>
                <th>Category ID</th>
                <th>Category Name</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
            <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                <tr>
                    <td><?php echo $category['category_id']; ?></td>
                    <td><?php echo $category['category_name']; ?></td>
                    <td>
                        <img src="<?php echo $category['image']; ?>" alt="<?php echo $category['category_name']; ?>" style="width: 50px; height: 50px; border-radius: 5px;">
                    </td>
                    <td>
                        <a href="edit_category.php?id=<?php echo $category['category_id']; ?>"><button class="edit-button">Edit</button></a>
                        <a href="delete_category.php?id=<?php echo $category['category_id']; ?>" onclick="return confirm('Are you sure you want to delete this category?');"><button class="delete-button">Delete</button></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h2>Customers</h2>
        <table class="table">
            <tr>
                <th>Customer ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            <?php while ($customer = mysqli_fetch_assoc($customers)): ?>
                <tr>
                    <td><?php echo $customer['customer_id']; ?></td> <!-- Assuming 'id' is the correct column name -->
                    <td><?php echo $customer['username']; ?></td>
                    <td><?php echo $customer['email']; ?></td>
                    <td>
                        <a href="edit_customer.php?id=<?php echo $customer['customer_id']; ?>"><button class="edit-button">Edit</button></a>
                        <a href="delete_customer.php?id=<?php echo $customer['customer_id']; ?>" onclick="return confirm('Are you sure you want to delete this customer?');"><button class="delete-button">Delete</button></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h2>Products</h2>
        <table class="table">
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
            <?php while ($product = mysqli_fetch_assoc($products)): ?>
                <tr>
                    <td><?php echo $product['product_id']; ?></td>
                    <td><?php echo $product['product_name']; ?></td>
                    <td><?php echo $product['price']; ?></td>
                    <td>
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['product_name']; ?>" style="width: 50px; height: 50px; border-radius: 5px;">
                    </td>
                    <td>
                        <a href="edit_product.php?product_id=<?php echo htmlspecialchars($product['product_id']); ?>" class="edit-button">Edit</a>
                        <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');"><button class="delete-button">Delete</button></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

    <footer>
        &copy; <?php echo date("Y"); ?> GMART SMART
    </footer>
</body>
</html>
