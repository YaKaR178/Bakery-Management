<?php
/**
 * File Name: catalog.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2024-12-20
 * Last Modified: 2024-12-30
 * Description:
 * This page displays a list of items for sale, with the option to view featured items or regular products, 
 * depending on the page. The features of the page include:
 *  - **Featured Items**: If the current page is the home page (index.php), it will display items marked as "in promotion."
 *  - **Pagination**: Displays a limited number of items per page (8 items per page) with navigation links to browse through all available products.
 *  - **Stock and Quantity Control**: Each item shows its stock status and allows users to adjust the quantity in their cart. The cart is stored in a session.
 *  - **Price Display**: The price of each item is shown in Israeli Shekels (₪).
 *  - **Out of Stock**: If an item is out of stock, a message is displayed indicating the item is unavailable.
 * 
 * The page interacts with the database to retrieve item details (SKU, name, price, image, stock status), 
 * and allows users to add or remove items from their shopping cart.
 * 
 */



// require the configure the connection to db
require_once 'db_con.php';
$conn = connectToDatabase();

// define the type of page (homepage promotion products/whole catalog order page)
$currentPHP = basename($_SERVER['PHP_SELF']);


// limitation for pagination
$limit = 8;

// define page and offset for pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// query for pagination
$resultCount = $conn->query("SELECT COUNT(*) AS total_items FROM items");
$totalItmes = $resultCount->fetch_assoc()['total_items'];


// calculate the number of pages
$totalPages = ceil($totalItmes/ $limit);

// check which page the user on 
if ($currentPHP == 'index.php') {
    // if the user on homepage, show only the promotion products
    $sql = "SELECT SKU, item_name, price, image, in_stock FROM items WHERE in_promotion = 1 ORDER BY in_stock DESC";
} else {
    // if the user on order page, show all the products
    $sql = "SELECT SKU, item_name, price, image, in_stock FROM items ORDER BY in_stock DESC LIMIT $limit OFFSET $offset";
}

// save the array of all products in result varible 
$result = $conn->query($sql);

// if the cart is empty, save an empty session
if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}
?>


<!--Feature-->
<section id="product1" class="section-p1">
    <!--<h2>Featured Items</h2>-->
    <p>Explore our collection</p>
    <div class="pro-container" id="pro">

        <?php
        // if result of products query is not empty
        if ($result->num_rows > 0) {
            
            // while loop for extracting the array of products
            while ($row = $result->fetch_assoc()) {
                $sku = $row['SKU']; // save the SKU of product
                $qty = $_SESSION['basket'][$sku] ?? 0; // save the quantity in session
                ?>
                <!-- Display product image -->
                <div class="pro" id="pro-<?php echo $sku; ?>">
                    <img src="<?php echo 'img/feature/'.$row['image']; ?>" alt="Item <?php echo $sku; ?>">
                    <div class="des">
                        <!-- Display product name -->
                        <h5><?php echo $row['item_name']; ?></h5>
                        <div class="price-quantity">
                            <!-- Display product price -->
                            <h4>&#8362; <?php echo $row['price']; ?></h4>
                            <?php if ($row['in_stock']): ?>
                                <!-- Show quantity control buttons if the item is in stock -->
                                <div class="buttons">
                                    <i onclick="decrement('<?php echo $sku; ?>')" class="fas fa-minus"></i>
                                    <div id="qty-<?php echo $sku; ?>" class="quantity"><?php echo $qty; ?></div>
                                    <i onclick="increment('<?php echo $sku; ?>')" class="fas fa-plus"></i>
                                </div>
                            <?php else: ?>
                                 <!-- Display "Out of Stock" message if the item is unavailable -->
                                <p>Out of Stock</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            // Display a message if no items are available
            echo "<p>No items available</p>";
        }
        // Close the database connection
        $conn->close();
        ?>
    </div>
</section>


