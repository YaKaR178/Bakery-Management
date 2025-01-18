<!--Header-->
<?php include 'Style/header.php';?>

<?php
/**
 * File Name: order.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2025-12-10
 * Last Modified: 2025-01-01
 * This PHP script builds a promotional holiday webpage for a pastries and sweets business. It incorporates a header, a hero section highlighting discounts, a dynamic product catalog, and a paginated navigation system. Additionally, it includes interactive features like a copyable coupon code and external scripts for enhanced functionality.

 * Features:
 * - **Header Integration**:
 *   - Includes a common header file for consistent styling and navigation.
 * - **Hero Section**:
 *   - Promotes a holiday discount with a coupon code users can copy using an interactive button.
 *   - Displays a notification when the coupon code is copied.
 * - **Product Catalog**:
 *   - Dynamically loads products from an external PHP file.
 * - **Pagination**:
 *   - Implements navigation for multi-page product listings.
 *   - Highlights the current page and provides navigation links to previous and next pages.
 * - **Script Integration**:
 *   - Includes `copy.js` to handle the coupon code copying action.
 */
?>

<!--Hero-->
<section id="page-header">
    <h1>Happy Holidays!</h1>
    <h4 id="save">Save 10% off holdiay pastries and sweets</h4>
    <p>Use the code <b id="coupon-code">HOL10</b> at checkout</p>
    <i class="fas fa-copy copy-icon" onclick="copyCouponCode()"></i>
    <div id="copy-notification" style="display:none;">Copied!</div>
</section>

<!--Products-->
<?php include 'catalog.php';?>



 <!-- Pagination -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">Previous</a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" <?= $i == $page ? 'style="background-color: #ddd;"' : '' ?>><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>">Next</a>
    <?php endif; ?>
</div>

<!--Scripts-->
<script src="copy.js"></script>

<?php include 'Style/newsletter.php';?>
<?php include 'Style/footer.php';?>




