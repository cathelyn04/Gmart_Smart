<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include('../db.php');

// Check if category ID is passed
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    // Fetch the category data
    $query = "SELECT * FROM categories WHERE category_id = $category_id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $category = mysqli_fetch_assoc($result);
    } else {
        echo "Category not found.";
        exit();
    }
} else {
    echo "No category ID specified.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'];

    // Update query
    $update_query = "UPDATE categories SET category_name = '$category_name' WHERE category_id = $category_id";

    if (mysqli_query($conn, $update_query)) {
        header("Location: manage_categories.php?success=Category updated successfully");
        exit();
    } else {
        echo "Failed to update category: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 100px;
            height: auto;
            margin-right: 20px;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 16px;
            margin-bottom: 8px;
            text-align: left;
        }

        input[type="text"] {
            padding: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #218838;
        }

        .back-link {
            text-decoration: none;
            color: #007bff;
            cursor: pointer;
            font-size: 16px;
            display: inline-block;
            margin-top: 20px;
        }

        .back-link:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>

<header>
    <img src="../assets/images/website/logo.jpg" alt="Logo" class="logo">
    <h1>Edit Category</h1>
</header>

<div class="container">
    <form method="POST">
        <label for="category_name">Category Name:</label>
        <input type="text" name="category_name" value="<?php echo $category['category_name']; ?>" required>
        <button type="submit">Update Category</button>
    </form>
    <a href="dashboard.php" class="back-link">Back to Dashboard</a>
</div>

</body>
</html>