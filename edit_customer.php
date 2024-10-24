<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include('../db.php'); // Adjust the path as needed

// Check if customer ID is passed
if (isset($_GET['id'])) {
    $customer_id = $_GET['id'];

    // Fetch customer details
    $query = "SELECT * FROM customers WHERE customer_id = $customer_id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $customer = mysqli_fetch_assoc($result);
    } else {
        echo "Customer not found.";
        exit();
    }
} else {
    echo "No customer ID specified.";
    exit();
}

// Handle form submission for editing the customer
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Update customer details
    $update_query = "UPDATE customers SET username = '$name', email = '$email' WHERE customer_id = $customer_id";

    if (mysqli_query($conn, $update_query)) {
        header("Location: manage_customers.php?success=Customer updated successfully");
        exit();
    } else {
        echo "Error updating customer: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
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
    <h1>Edit Customer</h1>

    <form action="" method="post">
        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo $customer['username']; ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo $customer['email']; ?>" required><br>

        <input type="submit" name="update" value="Update Customer">
    </form>
    <a href="dashboard.php" class="back-link">Back to Dashboard</a>
</body>
</html>

