<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include('../db.php'); // Adjust the path as needed

// Check if category ID is passed
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    // Fetch the category to confirm it exists
    $query = "SELECT * FROM categories WHERE category_id = $category_id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        // If the category exists, proceed with deletion
        $delete_query = "DELETE FROM categories WHERE category_id = $category_id";
        
        if (mysqli_query($conn, $delete_query)) {
            // Redirect after successful deletion
            header("Location: manage_categories.php?success=Category deleted successfully");
            exit();
        } else {
            echo "Failed to delete category: " . mysqli_error($conn);
        }
    } else {
        echo "Category not found.";
        exit();
    }
} else {
    echo "No category ID specified.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Category</title>
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

    <h1>Delete Category</h1>
    
    <!-- Deletion logic and confirmation message will go here if needed -->
</body>
</html>