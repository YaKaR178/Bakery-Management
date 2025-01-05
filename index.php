
<?php
/**
 * File Name: index.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2025-01-01
 * Last Modified: 2025-01-01
 * Description:
 * This PHP script assembles a index Homepage for the Moroccan Pastries business called
 * "From The Heart" bakery. It includes various sections such as a header, a hero section with a promotional offer, a product catalog, and a footer with a newsletter subscription. The hero section features key messaging and a call-to-action for users to place an order, while the footer offers additional engagement options.

 * Features:
 * - **Header and Footer Integration**:
 *   - Includes a common header and footer for consistent page layout.
 * - **Hero Section**:
 *   - Displays promotional text highlighting holiday offers and a call-to-action button that redirects users to the order page.
 * - **Catalog Integration**:
 *   - Includes a catalog section that dynamically loads the product list from an external PHP file. */
?>

<?php
include 'Style/header.php'; 
?>

<!--Hero-->
<section id="hero">
    <h4>Your Holiday Table - Sorted</h4>
    <h2>Moroccan Pastries</h2>
    <h1>From the Heart</h1>
    <p>Save more with our promo offers</p>
    <a href="order.php">Order Now</a>
</section>

<!--Feature-->
<?php include 'catalog.php';?>

<!--Footer-->  
<?php 
require 'Style/newsletter.php';
require 'Style/footer.php'; ?>