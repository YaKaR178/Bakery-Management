<?php
/**
 * File Name: payment_cancel.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2025-12-27
 * Last Modified: 2025-01-01
 * Description:
 * This script handles scenarios where an order's payment process fails in the 'From The Heart' bakery system.
 * 
 * - **Session Handling**: Checks if a user is logged in and determines their admin status using session variables.
 * - **Order Status Update**: Attempts to update the order status in the database to '6' (indicating "failed payment") 
 *   using the `order_id` stored in the session.
 * - **Error Handling**: Displays an error message if the database update query fails.
 * - **Frontend Integration**: Includes a header and footer for consistent layout, and displays a "Purchase failed" message to the user.
 * 
 * Features:
 * - **Database Interaction**: Executes a parameterized SQL query to securely update the order status.
 * - **Secure Coding Practices**: Uses prepared statements to prevent SQL injection.
 * - **User Feedback**: Provides a clear indication of payment failure on the page.
 */
session_start();
$isLoggedIn = isset($_SESSION['username']);
$isAdmin = $_SESSION['is_admin'] ?? false;

// Include database connection
include('db_connection.php');

// Get the order ID from the query string or form data (depending on how it's passed)
$order_id = $_SESSION['order_id']; 

// Update the status of the order in the database to '2' (paid)
$query = "UPDATE orders SET status = 6 WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);

if ($stmt->execute()) {
} else {
    // Handle failure to update the database
    echo "Error: Could not update order status.";
}

// Close the database connection
$stmt->close();
$conn->close();
?>


<?php include 'Style/header.php';?>

<!--Hero-->
<section id="page-header" class="checkout-header">
    <h1>Purchase failed</h1>
</section>

<?php include 'Style/footer.php';?>