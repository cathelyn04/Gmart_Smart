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

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        // If the customer exists, proceed with deletion
        $delete_stmt = $conn->prepare("DELETE FROM customers WHERE customer_id = ?");
        $delete_stmt->bind_param("i", $customer_id);
        
        if ($delete_stmt->execute()) {
            header("Location: manage_customers.php?success=Customer deleted successfully");
            exit();
        } else {
            echo "Error deleting customer: " . $conn->error;
        }
    } else {
        echo "Customer not found.";
        exit();
    }
} else {
    echo "No customer ID specified.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Customer</title>
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
    <a href="dashboard.php" class="back-link">Back to Dashboard</a>

    <h1>Delete Customer</h1>
</body>
</html>
