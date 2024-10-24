<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include '../db.php';

// Check if the category ID is set in the URL
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a category with the given ID exists
    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
    } else {
        echo "Category not found.";
        exit();
    }
} else {
    echo "No category ID provided.";
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($category['name']); ?> - Category Details</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f9fc;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            padding: 20px;
        }

        header {
            display: flex;
            align-items: center;
            background-color: #333;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        h1 {
            color: #ffffff;
            margin-left: 15px;
            font-size: 24px;
            font-weight: 600;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        img {
            width: 100%;
            height: auto;
            max-width: 400px;
            border-radius: 8px;
            margin: 20px 0;
        }

        p {
            font-size: 18px;
            color: #333;
        }

        a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }

        .back-link {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            background-color: #007bff;
            color: white;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <header>
        <h1><?php echo htmlspecialchars($category['name']); ?></h1>
    </header>

    <div class="container">
        <?php if (!empty($category['image'])): ?>
            <img src="<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
        <?php endif; ?>
        
        <p><strong>Category Name:</strong> <?php echo htmlspecialchars($category['name']); ?></p>
        
        <a class="back-link" href="manage_categories.php">Back to Categories</a>
    </div>
</body>

</html>
