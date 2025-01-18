<!--Header-->
<?php include_once 'Style/header.php'; ?>

<?php
/**
 * File Name: checkout.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2024-12-19
 * Last Modified: 2024-12-30
 * Description:
 * This page handles the checkout process for users who are ready to finalize their purchase. It allows users to 
 * enter personal and shipping details and proceed with the payment securely through PayPal. The features of the 
 * page include:
 *  - **Personal Details**: Users are prompted to enter their first name, last name, email address, and phone number.
 *  - **Shipping Address**: Users must provide a shipping address, including street address, city, and zip code.
 *  - **Total Price Display**: The total price of the order is calculated and displayed, including any applicable 
 *    discounts or promotions. The total is shown in Israeli Shekels (₪).
 *  - **Basket Data**: The page retrieves the user's shopping basket from local storage and prepares it for inclusion 
 *    in the order.
 *  - **Secure Payment**: Upon form submission, users are redirected to PayPal to complete the payment. The order 
 *    details, including the total price, are sent securely to PayPal for processing.
 *  - **Order Confirmation**: After successful payment, users will be notified, and the order ID is saved in the session 
 *    for tracking purposes.
 * 
 * The page interacts with the database to insert order details into the 'orders' and 'orderDetails' tables. It 
 * processes form submissions and communicates with PayPal for secure transactions.
 * 
 */

// session start for save order id
 session_start();

// require the configure the connection to db
require "db_con.php";
$conn = connectToDatabase();

// check if we get POST from the front
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $totalPrice = $_POST['total_price'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $zip = $_POST['zip'];
    $status = 0; // pending payment status

    // validate required fields
    if (!empty($email) && !empty($totalPrice) && !empty($address) && !empty($city) && !empty($zip)) {
        // concatenate address and city into a single string
        $fullAddress = $address . ", " . $city . " " . $zip;

        // prepare the SQL query for inserting into the 'orders' table
        $stmt = $conn->prepare(
            "INSERT INTO orders (username, payment, address, status) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("sdsi", $email, $totalPrice, $fullAddress, $status);

        // execute the query
        if ($stmt->execute()) {
            // successfully inserted into the database
            $orderId = $conn->insert_id; // get the order ID of the last inserted order
            $_SESSION['order_id'] = $orderId; // save order ID for tracking

            // insert each item from the basket into the 'order_details' table
            $basket = json_decode($_POST['basket'], true) ?? [];
            if (!empty($basket)) {
                // prepare SQL for inserting order details
                $detailStmt = $conn->prepare("INSERT INTO orderDetails (order_id, SKU, quantity) VALUES (?, ?, ?)");
            foreach ($basket as $sku => $quantity) {

                    // bind the parameters to the statement (order_id, sku, quantity)
                    $detailStmt->bind_param("iii", $orderId, $sku, $quantity);

                    // execute the statement to insert the order details
                    $detailStmt->execute();
                }
                $detailStmt->close();
            }

            // redirect to PayPal with the required data
            echo  "
            <form id='paypalForm' action='paypal_payment.php' method='POST'>
                <input type='hidden' name='total_price' value='{$totalPrice}'>
                <input type='hidden' name='order_id' value='{$_SESSION['order_id']}'>
                <input type='hidden' name='description' value='{$description}'>
            </form>
            <script>
                document.getElementById('paypalForm').submit();
            </script>";
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "All fields are required.";
    }
}

?>

<!--Hero-->
<section id="page-header" class="checkout-header">
    <h1>Checkout</h1>
</section>

<!--Checkout Form-->
<section id="checkout-form">
    <div>
        <form id="checkoutForm" action="checkout.php" method="POST">
            <h2>Personal Details</h2>
            <label for="firstName" class="required">First Name</label>
            <input type="text" id="firstName" name="firstName" required>
            <span class="error-message" id="firstNameError">Please enter your first name.</span>

            <label for="lastName" class="required">Last Name</label>
            <input type="text" id="lastName" name="lastName" required>
            <span class="error-message" id="lastNameError">Please enter your last name.</span>

            <label for="email" class="required">Email</label>
            <input type="email" id="email" name="email" required>
            <span class="error-message" id="emailError">Please enter a valid email address.</span>

            <label for="phone" class="required">Phone Number</label>
            <input type="tel" id="phone" name="phone" required>
            <span class="error-message" id="phoneError">Please enter a valid phone number.</span>

            <h2>Shipping Address</h2>
            <label for="address" class="required">Street Address</label>
            <input type="text" id="address" name="address" required>
            <span class="error-message" id="addressError">Please enter your street address.</span>

            <label for="city" class="required">City</label>
            <input type="text" id="city" name="city" required>
            <span class="error-message" id="cityError">Please enter your city.</span>

            <label for="zip" class="required">Zip Code</label>
            <input type="text" id="zip" name="zip" pattern="\d{7}" title="Zip code must be 7 digits" required>
            <span class="error-message" id="zipError">Please enter a 7-digit zip code.</span>

            <h2 class="cart-total">Total: <span id="total-price-display">0.00</span></h2>

            
            <input type="hidden" id="total_price" name="total_price" value="">


            <button type="submit" id="checkout">Secure Payment using PayPal</button>
        </form>

        <div id="label" class="text-center"></div>

    </div>
</section>

<?php include "Style/newsletter.php"; ?>
<?php include "Style/footer.php"; ?>

<script src="cart.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the final price from localStorage
    const finalPrice = localStorage.getItem('finalPrice');
    
    // Update both the display and hidden input
    if (finalPrice) {
        document.getElementById('total-price-display').textContent = `\u20AA${parseFloat(finalPrice).toFixed(2)}`;
        document.getElementById('total_price').value = parseFloat(finalPrice).toFixed(2);
    } else {
        // If no discount, get the regular total from the cart
        const cartTotal = document.querySelector('.cart-total');
        if (cartTotal) {
            const totalPrice = parseFloat(cartTotal.textContent.replace(/[^0-9.]/g, ''));
            document.getElementById('total-price-display').textContent = `\u20AA${totalPrice.toFixed(2)} \u20AA`;
            document.getElementById('total_price').value = totalPrice.toFixed(2);
        }
    }
});


document.addEventListener('DOMContentLoaded', function() {
    // retrieve the 'basket' object from localStorage, or initialize it as an empty object if not present
    const basket = JSON.parse(localStorage.getItem('basket')) || {}; 
    
    // create a hidden input field to store the basket data
    const basketInput = document.createElement('input');
    basketInput.type = 'hidden'; // set the input type to 'hidden' so it's not visible on the page
    basketInput.name = 'basket'; // name the input field to identify it in the form data
    basketInput.value = JSON.stringify(basket); // store the basket data as a JSON string in the input's value
    
    // append the hidden input field to the form with ID 'checkoutForm'
    document.getElementById('checkoutForm').appendChild(basketInput); 
});
</script>