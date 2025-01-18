<!--Header-->
<?php include_once 'Style/header.php'; ?>

<?php
/**
 * File Name: admin_coupons.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2024-12-19
 * Last Modified: 2024-12-30
 * Description:
 * This page allows the admin to manage discount coupons for the system. 
 * The admin can perform the following actions:
 *  - Add new coupons by specifying the coupon code, descriptive name, end date, and percentage of discount.
 *  - Remove existing coupons from the system.
 *  - Edit details of existing coupons, including the code, name, expiry date, and discount percentage.
 * 
 * Each coupon includes:
 *  - Coupon Code: A unique identifier for the coupon.
 *  - Descriptive Name: A meaningful name for the coupon.
 *  - End Date: The expiration date of the coupon.
 *  - Percentage of Discount: The discount rate offered by the coupon.
 * 
 * This page is accessible only to authenticated admin users.
 * 
 */

 // require the configure the connection to db
require_once 'db_con.php';
$conn = connectToDatabase();

// check if we get POST from the front
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_coupon'])) {
        // handle adding a coupon
        $couponCode = $_POST['coupon_code'];
        $couponName = $_POST['coupon_name'];
        $couponDate = $_POST['end_date'];
        $couponDiscount = $_POST['discount'];

        // prepare the insertion of all fields
        $stmt = $conn->prepare("INSERT INTO coupons (coupon_code, coupon_name, end_date, discount) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $couponCode, $couponName, $couponDate, $couponDiscount);

        if ($stmt->execute()) {
            $message = "Coupon added successfully!";
        } else {
            if ($conn->errno == 1062) {
                $message = "Error: Coupon code already exists!";
            } else {
                $message = "Error adding coupon: " . $stmt->error;
            }
        }

        $stmt->close();

    } elseif (isset($_POST['delete_coupon'])) {
        // handle deleting a coupon
        $selectedCouponCode = $_POST['coupon_code'];
        //prepare te deletion of coupon by coupon_code
        $stmt = $conn->prepare("DELETE FROM coupons WHERE coupon_code = ?");
        $stmt->bind_param("s", $selectedCouponCode);

        if ($stmt->execute()) {
            $message = "Coupon deleted successfully!";
        } else {
            $message = "Error deleting coupon: " . $stmt->error;
        }

        $stmt->close();

    } elseif (isset($_POST['edit_coupon'])) {
        // handle editing a coupon
        $selectedCouponCode = $_POST['coupon_code'];
        $newExpiryDate = !empty($_POST['edit_date']) ? $_POST['edit_date'] : null;
        $newDiscount = !empty($_POST['edit_discount']) ? $_POST['edit_discount'] : null;

        // build update query based on provided fields
        $sql = "UPDATE coupons SET ";
        $params = [];
        $types = "";

        if ($newExpiryDate) {
            $sql .= "end_date = ?, ";
            $params[] = $newExpiryDate;
            $types .= "s";
        }

        if ($newDiscount) {
            $sql .= "discount = ?, ";
            $params[] = $newDiscount;
            $types .= "i";
        }

        // if no updates were provided, show message
        if (!$newExpiryDate && !$newDiscount) {
            $message = "No changes provided to update!";
        } else {
            // remove trailing comma and space
            $sql = rtrim($sql, ", ");
            $sql .= " WHERE coupon_code = ?";
            $params[] = $selectedCouponCode;
            $types .= "s";

            // prepare and execute the query
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param($types, ...$params);

                if ($stmt->execute()) {
                    $message = "Coupon updated successfully!";
                } else {
                    $message = "Error updating coupon: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $message = "Error preparing statement: " . $conn->error;
            }
        }
    }
}

// fetch all coupons from the database for dropdown lists
$result = $conn->query("SELECT coupon_code, coupon_name FROM coupons");
$coupons = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $coupons[] = $row;
    }
}
$conn->close();

//end of server side
?>

<section id="edit-coupons" class="edit-coupons">

    <div class="wrapper" id="sign-up">
        <form action="admin_coupons.php" method="POST">
            <h1>Edit Coupons</h1>


            <div class="edit-coupons-section">

                <!--adding coupons-->
                <h3>Add Coupon</h3>

                <div class="input-box">
                    <input type="text" name="coupon_name" placeholder="Coupon Name" required>
                </div>

                <div class="input-box">
                    <input placeholder="Expiration Date" name="end_date" class="textbox-n" type="text" onfocus="(this.type='date')"
                            onblur="(this.type='text')" required>
                </div>

                <div class="input-box">
                    <input type="number" name="discount" step="1" placeholder="Discount (%)" required>
                </div>

                <div class="input-box">
                    <input type="text" name="coupon_code" placeholder="Coupon Code" required>
                </div>


                <div class="input-box">
                    <button type="submit" name="add_coupon" class="btn">Add</button>
                </div>

            </div>

        </form>


        <div class="edit-coupons-section">
            
            <!--Delete Coupons-->
            <h3>Delete Coupon</h3>

            <form action="admin_coupons.php" method="POST">
                <select name="coupon_code" id="coupon_code" class="coupon-code-dropdown" required>
                    <option value="" disabled selected>-- Select a Coupon --</option>
                    <?php foreach ($coupons as $coupon): ?>
                    <option value="<?php echo htmlspecialchars($coupon['coupon_code']); ?>">
                        <?php echo htmlspecialchars($coupon['coupon_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="delete_coupon" class="btn">Delete</button>
            </form>

        </div>



        <div class="edit-coupons-section">
<!--Update Exist Coupons-->
<h3>Update Coupon</h3>

<form action="admin_coupons.php" method="POST">
    <!-- Coupon selection -->
    <div class="input-box">
        <select name="coupon_code" id="coupon_code" class="coupon-code-dropdown" required>
            <option value="" disabled selected>-- Select a Coupon --</option>
            <?php foreach ($coupons as $coupon): ?>
                <option value="<?php echo htmlspecialchars($coupon['coupon_code']); ?>">
                    <?php echo htmlspecialchars($coupon['coupon_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Edit Expiration Date -->
    <div class="input-box">
        <label for="edit-date">Edit Date Ex</label>
        <input placeholder="New Expiration Date" name="edit_date" class="textbox-n" type="text"
                onfocus="(this.type='date')" onblur="(this.type='text')" required>
    </div>

    <!-- Edit Discount -->
    <div class="input-box">
        <label for="edit-discount">Edit Discount</label>
        <input type="number" name="edit_discount" step="1" placeholder="New Discount (%)" required>
    </div>

    <!-- Submit Button -->
    <div class="input-box">
        <button type="submit" name="edit_coupon" class="btn">Edit</button>
    </div>
</form>
</div>

    </div>

</section>

<div style="height: 50px;"></div>


<?php include "Style/footer.php";?>

<?php
if (isset($message)) {
    echo "<script>alert('$message');</script>";
}
?>

</body>


</html>