<?php
// Start session at the very top
session_start();

// Include Database Connection
include_once('../db.php');

// Now retrieve user details if session user_id exists
$user_id = $_SESSION['user_id']; // Ensure this is set

$query = "SELECT * FROM customers WHERE customer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if query returned a result and set user data
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $first_name_database = $row['username']; // Assuming 'username' stores first name
    $email_database = $row['email'];
    $birthday_database = isset($row['birthday']) ? $row['birthday'] : ''; // Check if 'birthday' key exists
    $profile_picture_database = isset($row['profile_picture']) ? $row['profile_picture'] : ''; // Check if 'profile_picture' key exists
} else {
    // Handle the case where user data is not found (optional)
    $first_name_database = 'Unknown';
    $email_database = 'Unknown';
    $birthday_database = ''; // Set to empty if not found
    $profile_picture_database = '';
}

// Handle form submission for updating user details
$success_message = ''; // Initialize success message
$is_updated = false; // Flag to track if the profile was updated
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_birthday = mysqli_real_escape_string($conn, $_POST['birthday']);
    
    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "../uploads/"; // Ensure this folder exists
        $file_name = basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . uniqid() . '_' . $file_name; // Prevent filename collision
        $upload_ok = 1;
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check === false) {
            $upload_ok = 0;
        }

        // Check file size (limit to 2MB for example)
        if ($_FILES["profile_picture"]["size"] > 2000000) {
            $upload_ok = 0;
        }

        // Allow certain file formats
        if (!in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            $upload_ok = 0;
        }

        // Check if $upload_ok is set to 0 by an error
        if ($upload_ok == 0) {
            echo '<div class="alert alert-danger">Sorry, your file was not uploaded.</div>';
        } else {
            // If everything is ok, try to upload file
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                // Update the user's details in the database, including profile picture
                $update_query = "UPDATE customers SET username=?, email=?, birthday=?, profile_picture=? WHERE customer_id=?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("ssssi", $new_username, $new_email, $new_birthday, $target_file, $user_id);
                
                if ($update_stmt->execute()) {
                    $is_updated = true; // Set flag to true on successful update
                    // Optionally, refresh user data after update
                    $first_name_database = $new_username;
                    $email_database = $new_email;
                    $birthday_database = $new_birthday;
                    $profile_picture_database = $target_file; // Update the picture path
                } else {
                    echo '<div class="alert alert-danger">Error updating profile: ' . mysqli_error($conn) . '</div>';
                }
            } else {
                echo '<div class="alert alert-danger">Sorry, there was an error uploading your file.</div>';
            }
        }
    } else {
        // Update only the username, email, and birthday if no new picture is uploaded
        $update_query = "UPDATE customers SET username=?, email=?, birthday=? WHERE customer_id=?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("sssi", $new_username, $new_email, $new_birthday, $user_id);
        
        if ($update_stmt->execute()) {
            $is_updated = true; // Set flag to true on successful update
            // Optionally, refresh user data after update
            $first_name_database = $new_username;
            $email_database = $new_email;
            $birthday_database = $new_birthday;
        } else {
            echo '<div class="alert alert-danger">Error updating profile: ' . mysqli_error($conn) . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <style>
        .hidden {
            display: none;
        }

        .back-to-dashboard {
            color: blue;            /* Set the text color to blue */
            text-decoration: underline; /* Underline the text */
            margin-top: 20px;      /* Add a top margin for spacing */
            display: inline-block;  /* Keep it inline but allow margin */
        }
        
        .back-to-dashboard:hover {
            color: darkblue;       /* Darker shade on hover for better visibility */
        }
    </style>
    <script>
        // Function to display the success message after 5 seconds if the profile is updated
        function showSuccessMessage() {
            const isUpdated = <?php echo json_encode($is_updated); ?>;
            if (isUpdated) {
                setTimeout(() => {
                    const bottomMessageDiv = document.getElementById('bottom-message');
                    bottomMessageDiv.innerHTML = "Profile updated successfully.";
                    bottomMessageDiv.classList.remove('hidden');
                }, 5000);
            }
        }
    </script>
</head>
<body onload="showSuccessMessage()">
    <div class="container-fluid p-5">
        <!-- Profile Picture Section -->
        <div class="text-center mb-4">
            <?php if ($profile_picture_database): ?>
                <img src="<?php echo htmlspecialchars($profile_picture_database); ?>" alt="Profile Picture" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
            <?php else: ?>
                <img src="../assets/images/Website/account.png" alt="Default User Icon" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
            <?php endif; ?>
        </div>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Hello Dear <?php echo htmlspecialchars($first_name_database); ?></h5>
                        <p class="card-text">Your email is <?php echo htmlspecialchars($email_database); ?></p>
                        <p class="card-text">Your birthday is <?php echo htmlspecialchars($birthday_database); ?></p>
                        <a href="logout.php" class="btn btn-primary">Logout</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <h2>Update Profile</h2>
                <form method="post" action="" enctype="multipart/form-data"> <!-- Note the enctype -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($first_name_database); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email_database); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="birthday" class="form-label">Birthday</label>
                        <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo htmlspecialchars($birthday_database); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>

                <!-- Back to Dashboard Link -->
                <a href="explore.php" class="back-to-dashboard">Back to Dashboard</a>

        </div>
    </div>
</body>
</html>
