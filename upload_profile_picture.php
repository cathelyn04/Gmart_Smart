<?php
session_start();
include '../db.php';

// Directory where profile pictures will be uploaded
$target_directory = "../uploads/"; // Ensure this directory exists and is writable
$target_file = $target_directory . basename($_FILES["profile_picture"]["name"]);
$uploadOk = 1;

// Check if the file is a valid image
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
$check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
if ($check === false) {
    echo "<p>File is not an image.</p>";
    $uploadOk = 0;
}

// Check file size (limit to 2MB)
if ($_FILES["profile_picture"]["size"] > 2000000) {
    echo "<p>Sorry, your file is too large.</p>";
    $uploadOk = 0;
}

// Allow certain file formats
if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
    echo "<p>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p>";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk === 0) {
    echo "<p>Sorry, your file was not uploaded.</p>";
} else {
    // If everything is ok, try to upload the file
    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        // Update session variable for profile picture
        $_SESSION['profile_picture'] = $target_file;

        // Optionally, update the database to store the profile picture path
        $user_id = $_SESSION['user_id'];
        $update_query = "UPDATE customers SET profile_picture = ? WHERE customer_id = ?"; // Use 'customer' table
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('si', $target_file, $user_id);
        
        if ($stmt->execute()) {
            echo "<p>The file " . htmlspecialchars(basename($_FILES["profile_picture"]["name"])) . " has been uploaded and your profile has been updated.</p>";
        } else {
            echo "<p>Failed to update your profile picture in the database.</p>";
        }
    } else {
        echo "<p>Sorry, there was an error uploading your file.</p>";
    }
}

// Provide a link to return to the user account page
echo '<a href="user_account.php" class="back-link">Back to Account</a>';
?>