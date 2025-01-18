<?php
/**
 * File Name: payment_success.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2025-01-01
 * Last Modified: 2025-01-01
 * Description:
 * This script handles the post-payment success process for orders in the 'From The Heart' bakery system.
 * It performs the following actions:
 * 
 * - **Session Handling**: Checks if a user is logged in and determines their admin status.
 * - **Order Status Update**: Updates the status of the order in the database to '1' (indicating "paid") 
 *   based on the `order_id` stored in the session.
 * - **Error Handling**: Displays an error message if the database update fails.
 * - **UI Integration**: Includes the header and footer for consistent page structure and 
 *   displays a success message to the user.
 * - **Cart Management**: Calls the `clearCart` function from `cart.js` upon page load to reset the user's cart.
 * 
 * Features:
 * - **Database Interaction**: Uses a prepared statement to update the order status securely.
 * - **Secure Coding Practices**: Prevents SQL injection through parameterized queries.
 * - **Frontend and Backend Coordination**: Ensures a seamless user experience by integrating frontend scripts 
 *   and backend processes.
 * 
 */
session_start();
$isLoggedIn = isset($_SESSION['username']);
$isAdmin = $_SESSION['is_admin'] ?? false;

// Include database connection
include('db_con.php');
$conn = connectToDatabase();

// Get the order ID from the query string or form data (depending on how it's passed)
$order_id = $_SESSION['order_id']; 

// Update the status of the order in the database to '2' (paid)
$query = "UPDATE orders SET status = 1 WHERE order_id = ?";
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
    <h1>Payment Success! thank you for purchasing at 'From The Heart' bakery. Your order ID is: <?php echo htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8'); ?></h1>
</section>

<?php include 'Style/footer.php';?>

<script>
    // Ensure the cart.js script is loaded, then call clearCart() when the page loads
    document.addEventListener("DOMContentLoaded", function() {    
        clearCart();  // Call the clearCart function from cart.js
    });
</script>