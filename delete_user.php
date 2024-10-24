<?php
include '../db.php';

if (isset($_POST['customer_id'])) {
    $customer_id = intval($_POST['customer_id']);

    // Delete the user
    $delete_query = "DELETE FROM customers WHERE customer_id = $customer_id";
    if ($conn->query($delete_query)) {
        echo 'success'; // Send success response back to AJAX
    } else {
        echo 'error'; // Send error response back to AJAX
    }
}
?>
