<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include '../db.php';

// Fetch all users from the database
$query = "SELECT customer_id, username, email FROM customers ORDER BY customer_id ASC";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .action-links a {
            margin-right: 10px;
        }
        .delete-btn {
            color: red;
            cursor: pointer;
        }

        .back-button {
            margin-top: 20px;
            text-align: left;
        }

        .back-button button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-button button:hover {
            background-color: #0056b3;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function deleteUser(customer_id) {
            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: 'delete_user.php',
                    type: 'POST',
                    data: { customer_id: customer_id },
                    success: function(response) {
                        if (response == 'success') {
                            // Remove the user row from the table
                            $('#user_' + customer_id).remove();
                        } else {
                            alert('Error: Unable to delete user.');
                        }
                    }
                });
            }
        }
    </script>
</head>
<body>
    <h1>Manage Users</h1>
    
    <!-- Display the list of users -->
    <table>
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr id="user_<?php echo $row['customer_id']; ?>">
                        <td><?php echo $row['customer_id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td class="action-links"> 
                            <span class="delete-btn" onclick="deleteUser(<?php echo $row['customer_id']; ?>)">Delete</span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Back to Dashboard Button -->
    <div class="back-button">
        <button onclick="window.location.href='dashboard.php'">Back to Dashboard</button>
    </div>
</body>
</html>
