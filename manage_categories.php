<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include '../db.php';

$error_message = ""; // Initialize an error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];

    // Check if the category already exists
    $check_query = "SELECT * FROM categories WHERE category_name = '$name'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        $error_message = "Category already exists. Please choose a different name."; // Set the error message
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
                    // Insert category name and image path into the database
                    $query = "INSERT INTO categories (category_name, image) VALUES ('$name', '$target_file')";
                    $conn->query($query);
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            } else {
                echo "Only JPG, JPEG, PNG & GIF files are allowed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Categories</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f9fc;
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            margin: 0;
            flex-direction: column;
            min-height: 100vh; /* Allow the body to expand */
        }

        header {
            display: flex;
            align-items: center; /* Align logo and title vertically */
            background-color: #333;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 100%; /* Make header cover full width */
        }

        h1 {
            color: #ffffff;
            margin-left: 15px;
            font-size: 24px;
            font-weight: 600;
        }

        .logo {
            width: 60px;
            height: auto;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center; /* Center form contents */
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            margin: 20px 0; /* Add some vertical margin */
        }

        input[type="text"],
        input[type="file"] {
            width: 90%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f9f9f9;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px;
            width: 75%;
        }

        button:hover {
            background-color: #0056b3;
        }

        .container {
            text-align: center;
        }

        p {
            margin-top: 20px;
        }

        p a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }

        p a:hover {
            text-decoration: underline;
        }

        h2 {
            text-align: center;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        ul {
            list-style-type: none;
            padding: 0;
            display: flex; /* Use flexbox to arrange items in a row */
            flex-wrap: wrap; /* Allow wrapping to the next line if necessary */
            justify-content: center; /* Center items */
            max-width: 100%; /* Adjust to the full width */
            margin: 20px 0; /* Add some vertical margin */
        }

        li {
            background-color: #ffffff;
            padding: 20px;
            margin: 10px; /* Adjust margin to create space between items */
            border-radius: 12px;
            display: flex;
            flex-direction: column; /* Align items in a column */
            justify-content: center; /* Center the contents */
            align-items: center; /* Center contents */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            width: 150px; /* Set a fixed width for the list items */
        }


        li img {
            width: 50px;
            height: 40px;
            object-fit: cover;
            border-radius: 5px;
            margin-left: 10px;
        }

        #error-message {
            display: none;
            color: #d9534f;
            font-size: 16px;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            width: 100%;
            max-width: 500px;
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script>
        function showError() {
            var message = document.getElementById("error-message");
            message.style.display = "block";
            setTimeout(function () {
                message.style.display = "none";
            }, 5000);
        }
    </script>
</head>

<body>
    <div class="container">
        <header>
            <img src="../assets/images/website/logo.jpg" alt="Logo" class="logo"> <!-- Logo image -->
            <h1>Manage Categories</h1>
        </header>
    </div>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Category Name" required>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit">Add Category</button>
    </form>

    <p>
        <a href="dashboard.php">Back to Dashboard</a>
    </p>

    <h2>Category List</h2>
    <ul>
        <?php
        $result = $conn->query("SELECT * FROM categories");
        while ($row = $result->fetch_assoc()) {
            echo "<li><a href='category_details.php?id=" . $row['category_id'] . "'>" . $row['category_name'] . "</a>"; // Make category name clickable
            if (!empty($row['image'])) {
                echo "<img src='" . $row['image'] . "' alt='" . $row['category_name'] . "'>";
            }
            echo "</li>";
        }
        ?>
    </ul>

    <div id="error-message">
        <?php
        if ($error_message) {
            echo $error_message;
            echo '<script>showError();</script>';
        }
        ?>
    </div>
</body>

</html>
