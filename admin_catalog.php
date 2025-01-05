<?php

/**
 * File Name: admin_catalog.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2024-12-19
 * Last Modified: 2024-12-30
 * This page allows the admin to manage the product catalog. 
 * The admin can perform the following actions:
 *  - Add new products to the catalog by specifying the SKU, descriptive name, price, and uploading a picture.
 *  - Remove existing products from the catalog.
 *  - Update details of existing products, including SKU, name, price, picture, and availability.
 *  - Mark a product as "promoted" to feature it on the homepage.
 *  - Define whether a product is "out of stock," preventing it from being displayed to customers.
 * 
 * Each product in the catalog includes:
 *  - SKU: A unique identifier for the product.
 *  - Descriptive Name: A short, meaningful name for the product.
 *  - Price: The cost of the product.
 *  - Picture: An image representing the product.
 * 
 * This page is accessible only to authenticated admin users.
 */

 // require the configure the connection to db
require_once 'db_con.php';
$conn = connectToDatabase();

// define vars for path of pictures directory
$uploadDir = "img/feature/";
// define message to display to admin
$message = "";

// check if we get POST from the front
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //if admin choose to add item to catalog
    if (isset($_POST['add_item'])) {
        // handle adding a new item
        $itemName = $_POST['item_name']; //define descriptive name of item
        $itemPrice = $_POST['price']; //define item price
        $itemStock = isset($_POST['in_stock']) ? 1 : 0; //define if the item in stock or not
        $itemPromotion = isset($_POST['in_promotion']) ? 1 : 0; //define if item in promotion and will presented in homepage

        // handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name']; //sets temporary name for picture file
            $fileName = $_FILES['image']['name'];// Retrieve the original name of the uploaded file
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));// extract the file extension and convert it to lowercase

            // check if file extension is valid
            if (in_array($fileExtension, ['png', 'jpg', 'jpeg'])) {
                $newFileName = uniqid() . ".$fileExtension";
                $destPath = $uploadDir . $newFileName;

                //transfer the file to path
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    // insert into database
                    $stmt = $conn->prepare("INSERT INTO items (item_name, image, price, in_stock, in_promotion) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssiii", $itemName, $newFileName, $itemPrice, $itemStock, $itemPromotion);

                    if ($stmt->execute()) {
                        $message = "Item added successfully!";
                    } else {
                        $message = "Error adding item: " . $stmt->error;
                        unlink($destPath); // Remove uploaded file if DB insert fails
                    }
                    $stmt->close();
                } else {
                    $message = "Error moving uploaded file.";
                }
            } else {
                $message = "Invalid file type. Only PNG, JPG, and JPEG are allowed.";
            }
        } else {
            $message = "Error uploading file.";
        }
    } elseif (isset($_POST['delete_item'])) {
        // handle deleting an item
        $selectedItem = $_POST['SKU'];

        // get current image name to delete the file
        $stmt = $conn->prepare("SELECT image FROM items WHERE SKU = ?");
        $stmt->bind_param("i", $selectedItem);
        $stmt->execute();
        $stmt->bind_result($imageName);
        $stmt->fetch();
        $stmt->close();

        if ($imageName) {
            $stmt = $conn->prepare("DELETE FROM items WHERE SKU = ?");
            $stmt->bind_param("i", $selectedItem);

            if ($stmt->execute()) {
                $message = "Item deleted successfully!";
                @unlink($uploadDir . $imageName); // remove image file
            } else {
                $message = "Error deleting item: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Item not found.";
        }
    //if admin choose to edit catalog
    } elseif (isset($_POST['edit_item'])) {
        // handle editing an item
        $selectedItem = $_POST['SKU'];
        $newName = !empty($_POST['edit_name']) ? $_POST['edit_name'] : null;
        $newPrice = !empty($_POST['edit_price']) ? $_POST['edit_price'] : null;
        $newStock = isset($_POST['edit_stock']) ? 1 : 0;
        $newPromotion = isset($_POST['edit_promotion']) ? 1 : 0;

        // handle file upload for edit
        $newImage = null;
        if (isset($_FILES['edit_image']) && $_FILES['edit_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['edit_image']['tmp_name'];
            $fileName = $_FILES['edit_image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            //chek if file extension is valid
            if (in_array($fileExtension, ['png', 'jpg', 'jpeg'])) {
                $newImage = uniqid() . ".$fileExtension";
                $destPath = $uploadDir . $newImage;

                if (!move_uploaded_file($fileTmpPath, $destPath)) {
                    $message = "Error moving uploaded file.";
                    $newImage = null;
                }
            } else {
                $message = "Invalid file type for the new image.";
            }
        }

        // fetch current image to delete if a new one is uploaded
        if ($newImage) {
            $stmt = $conn->prepare("SELECT image FROM items WHERE SKU = ?");
            $stmt->bind_param("i", $selectedItem);
            $stmt->execute();
            $stmt->bind_result($currentImage);
            $stmt->fetch();
            $stmt->close();
        }

        // update item
        $sql = "UPDATE items SET ";
        $params = [];
        $types = "";

        if ($newName) {
            $sql .= "item_name = ?, ";
            $params[] = $newName;
            $types .= "s";
        }

        if ($newPrice) {
            $sql .= "price = ?, ";
            $params[] = $newPrice;
            $types .= "i";
        }

        if ($newImage) {
            $sql .= "image = ?, ";
            $params[] = $newImage;
            $types .= "s";
        }

        $sql .= "in_stock = ?, in_promotion = ? WHERE SKU = ?";
        $params[] = $newStock;
        $params[] = $newPromotion;
        $params[] = $selectedItem;
        $types .= "iii";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $message = "Item updated successfully!";
            if ($newImage && $currentImage) {
                @unlink($uploadDir . $currentImage); // remove old image
            }
        } else {
            $message = "Error updating item: " . $stmt->error;
            if ($newImage) {
                @unlink($uploadDir . $newImage); // remove new image on error
            }
        }
        $stmt->close();
    }
}

// fetch all items from the database for dropdown lists
$result = $conn->query("SELECT SKU, item_name, price, image, in_stock, in_promotion FROM items");
$items = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}
$conn->close();

//server side end.
?> 

<!--Header-->
<?php include "Style/header.php"; ?>

<section id="edit-catalog" class="edit-catalog">
    <div class="wrapper" id="sign-up">

        <!--add item form-->
        <form action="admin_catalog.php" method="POST" enctype="multipart/form-data">
            <h1>Edit Catalog</h1>

            <div class="edit-coupons-section">
                <h3>Add Item</h3>
                <div class="input-box">
                    <input type="text" name="item_name" placeholder="Item Name" required>
                </div>
                <div class="input-box">
                    <input placeholder="Price (&#8362;)" name="price" class="textbox-n" type="number" required>
                </div>
                <div class="input-box">
                    <input type="file" name="image" required>
                </div>
                <div class="input-box">
                    <label><input type="checkbox" name="in_stock" value="1" />In Stock</label>
                </div>
                <div class="input-box">
                    <label><input type="checkbox" name="in_promotion" value="1" />In Promotion</label>
                </div>
                <div class="input-box">
                    <button type="submit" name="add_item" class="btn">Add</button>
                </div>
            </div>
        </form>

        <!--delete item form-->
        <div class="edit-catalog-section">
            <h3>Delete Item</h3>
            <form action="admin_catalog.php" method="POST">
                <select name="SKU" class="edit-catalog-dropdown" required>
                    <option value="" disabled selected>-- Select an Item --</option>
                    <?php foreach ($items as $item): ?>
                        <option value="<?php echo htmlspecialchars($item['SKU']); ?>">
                            <?php echo htmlspecialchars($item['item_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="delete_item" class="btn">Delete</button>
            </form>
        </div>
        
        <!--edit catalog item form-->
        <div class="edit-catalog-section">
            <h3>Edit Item</h3>
            <form action="admin_catalog.php" method="POST" enctype="multipart/form-data">
                <div class="input-box">
                    <select name="SKU" id="edit-catalog-dropdown" class="edit-catalog-dropdown" required>
                        <option value="" disabled selected>-- Select an Item --</option>
                        <?php foreach ($items as $item): ?>
                            <option value="<?php echo htmlspecialchars($item['SKU']); ?>" 
                                    data-name="<?php echo htmlspecialchars($item['item_name']); ?>"
                                    data-price="<?php echo htmlspecialchars($item['price']); ?>"
                                    data-image="<?php echo htmlspecialchars($item['image']); ?>"
                                    data-stock="<?php echo $item['in_stock'] ? '1' : '0'; ?>"
                                    data-promotion="<?php echo $item['in_promotion'] ? '1' : '0'; ?>">
                                <?php echo htmlspecialchars($item['item_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input-box">
                    <label for="edit-name">Edit Name</label>
                    <input type="text" name="edit_name" id="edit-name" placeholder="New Item Name">
                </div>

                <div class="input-box">
                    <label for="edit-price">Edit Price</label>
                    <input type="number" name="edit_price" id="edit-price" placeholder="New Price (&#8362;)">
                </div>

                <div class="input-box">
                    <label for="edit-image">Edit Image</label>
                    <input type="file" name="edit_image" id="edit-image">
                </div>

                <div class="input-box">
                    <label><input type="checkbox" name="edit_stock" id="edit-stock"> In Stock</label>
                </div>

                <div class="input-box">
                    <label><input type="checkbox" name="edit_promotion" id="edit-promotion"> In Promotion</label>
                </div>

                <div class="input-box">
                    <button type="submit" name="edit_item" class="btn">Edit</button>
                </div>
            </form>
        </div>
    </div>
</section>

<!--script for set placeholders in item editor form-->
<script>
    document.getElementById('edit-catalog-dropdown').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        document.getElementById('edit-name').placeholder = selectedOption.dataset.name || '';
        document.getElementById('edit-price').placeholder = selectedOption.dataset.price || '';
        document.getElementById('edit-stock').checked = selectedOption.dataset.stock === '1';
        document.getElementById('edit-promotion').checked = selectedOption.dataset.promotion === '1';
    });
</script>

<!--Footer-->
<?php include "Style/footer.php"; ?>

<!--message to admin at the end of every process-->
<?php
if ($message) {
    echo "<script>alert('$message');</script>";
}
?>
