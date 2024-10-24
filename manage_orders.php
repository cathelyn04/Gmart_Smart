<?php
session_start();
require '../db.php'; // Ensure db.php is correctly configured

// Initialize a message variable
$message = '';

// Handle updating order status
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    // Fetch current status of the order
    $status_query = "SELECT status FROM order_status WHERE order_id = ? ORDER BY status_id DESC LIMIT 1";
    $status_stmt = $conn->prepare($status_query);
    $status_stmt->bind_param('i', $order_id);
    $status_stmt->execute();
    $status_stmt->bind_result($current_status);
    $status_stmt->fetch();
    $status_stmt->close();

    // Check if the order is already 'Complete' or 'Cancelled'
    if ($current_status == 'Complete' || $current_status == 'Cancelled') {
        $message = "Order ID $order_id is already $current_status and cannot be updated.";
    } else {
        // Update order status in the orders table
        $update_order_query = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stmt = $conn->prepare($update_order_query);
        
        if ($stmt) {
            $stmt->bind_param('si', $new_status, $order_id);
            
            if ($stmt->execute()) {
                // Insert into order_status table for logging the status change
                $insert_status_query = "INSERT INTO order_status (order_id, status) VALUES (?, ?)";
                $status_stmt = $conn->prepare($insert_status_query);
                if ($status_stmt) {
                    $status_stmt->bind_param('is', $order_id, $new_status);
                    if ($status_stmt->execute()) {
                        $message = "Order status updated to '$new_status' for Order ID: $order_id.";
                    } else {
                        $message = "Error logging status change: " . $status_stmt->error;
                    }
                } else {
                    $message = "Error preparing status logging statement: " . $conn->error;
                }
            } else {
                $message = "Error updating order status: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error preparing order update statement: " . $conn->error;
        }
    }
}

// Fetch order IDs and current statuses from the orders table
$order_query = "SELECT o.order_id, s.status 
                FROM orders o 
                JOIN order_status s ON o.order_id = s.order_id 
                WHERE s.status_id = (SELECT MAX(status_id) FROM order_status WHERE order_id = o.order_id) 
                ORDER BY o.order_id DESC";
$order_result = $conn->query($order_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #333;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            color: white;
            position: relative;
        }

        .logo {
            height: 50px; /* Adjust logo size */
            margin-right: 20px; /* Space between logo and title */
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 8px 8px;
        }

        .message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #d6e9c6;
            display: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f7f7f7;
            color: #333;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        select, button {
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #218838;
        }

        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .back-to-dashboard {
            text-align: center;
            margin-top: 20px;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var messageDiv = document.getElementById("message");
            if (messageDiv) {
                messageDiv.style.display = "block"; // Show message
                setTimeout(function() {
                    messageDiv.style.display = "none"; // Hide after 5 seconds
                }, 5000);
            }

            document.querySelectorAll('select[data-status]').forEach(function(select) {
                if (select.dataset.status === 'Complete' || select.dataset.status === 'Cancelled') {
                    select.disabled = true;
                }
            });
        });
    </script>
</head>
<body>
    <div class="header">
        <img src="../assets/images/website/logo.jpg" alt="Logo" class="logo">
        <h1>Manage Customer Orders</h1>
    </div>

    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="message" id="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form action="manage_orders.php" method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Current Status</th>
                        <th>Update Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $order_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td>
                                <select name="status" data-status="<?php echo htmlspecialchars($order['status']); ?>" required>
                                    <option value="Pending" <?php echo ($order['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Ready to Pickup" <?php echo ($order['status'] === 'Ready to Pickup') ? 'selected' : ''; ?>>Ready to Pickup</option>
                                    <option value="Complete" <?php echo ($order['status'] === 'Complete') ? 'selected' : ''; ?>>Complete</option>
                                    <option value="Cancelled" <?php echo ($order['status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <button type="submit" name="update_status" value="<?php echo htmlspecialchars($order['order_id']); ?>" <?php if ($order['status'] === 'Complete' || $order['status'] === 'Cancelled') echo 'disabled'; ?>>Update</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </form>

        <div class="back-to-dashboard">
            <a href="dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
