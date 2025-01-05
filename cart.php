<?php
/**
 * File Name: cart.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2024-12-19
 * Last Modified: 2024-12-30
 * Description:
 * This page displays the shopping cart for the user, allowing them to view the items they have added. 
 * The user can also apply a promotional code (coupon) to potentially receive a discount on their cart. 
 * The page includes the following features:
 *  - **Redeem Coupon**: A form where the user can enter a promo code to apply a discount to their cart. 
 *  - **Shopping Cart Display**: A section where the items in the user's cart are displayed, including details like product names, quantities, and total prices.
 *  - **Newsletter Signup**: A call-to-action to subscribe to the newsletter at the bottom of the page.
 * 
 * When the user submits a promo code, the page will validate the code and apply the appropriate discount to the cart.
 * 
 */
?>


<?php include 'Style/header.php';?>

    <!--Hero-->
    <section id="page-header">
        <h1>Shopping Cart</h1>
        <h4 id="save">We hope you found everything your heart desires</h4>
    </section>

    <!--Redeem Coupon-->
    <div id="label" class="text-center"></div>
    <div class="promo-code" id="promo-code">
        <input type="text" id="promo-code-input" name="promo_code" placeholder="Enter Promo Code">
        <button class="normal" name="promoCode">Apply</button>
    </div>


    <div class="shopping-cart" id="shopping-cart"></div>



<?php include 'Style/newsletter.php';?>
<?php include 'Style/footer.php';?>
