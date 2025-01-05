<?php

/**
 * File Name: admin_orders.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2024-12-28
 * Last Modified: 2024-12-30
 * Description: 
 * This page allows the admin to manage and view order transactions in the system. 
 * The admin can perform the following actions:
 *  - Edit the status of each order transaction (e.g., pending, shipped, delivered, canceled).
 *  - View detailed information about each order.
 * 
 * Each order includes the following details:
 *  - Timestamp: The date and time when the order was placed.
 *  - Order ID: A unique identifier for the order.
 *  - Customer Name: The full name of the customer who placed the order.
 *  - Address: The delivery address for the order.
 *  - Items Ordered: A list of items included in the order.
 *  - Payment Amount: The total amount paid for the order.
 *  - Current Status: The current status of the order transaction.
 * 
 * This page is accessible only to authenticated admin users.
 * 
 */

  // require the configure the connection to db
require_once 'db_con.php';
$conn = connectToDatabase();

// limitation for pagination
$limit = 8;

// define page and offset for pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// query for pagination
$resultCount = $conn->query("SELECT COUNT(DISTINCT order_id) AS total_orders FROM orderDetails");
$totalOrders = $resultCount->fetch_assoc()['total_orders'];

// main query for join orders table with orders details
$result = $conn->query("SELECT 
    o.order_id,
    o.order_time,
    o.username,
    o.address,
    GROUP_CONCAT(i.item_name SEPARATOR ', ') AS ordered_products,
    o.payment,
    o.status
FROM 
    orders o
JOIN 
    orderDetails od ON o.order_id = od.order_id
JOIN 
    items i ON od.SKU = i.SKU
GROUP BY 
    o.order_id
ORDER BY 
    o.status, o.order_id LIMIT $limit OFFSET $offset");

// check if we get POST from the front
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $status = isset($_POST['status']) ? intval($_POST['status']) : -1;

    // prepare the query for execution 
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    if (!$stmt) {
        die("Failed to prepare statement: " . $conn->error);
    }

    // bind the parameters for sql injection safety
    $stmt->bind_param("ii", $status, $order_id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: admin_orders.php');
        exit;
    } else {
        $stmt->close();
        $conn->close();
        die("Error updating status: " . $conn->error);
    }
}

// calculate the number of pages
$totalPages = ceil($totalOrders / $limit);
?>

<!--Header-->
<?php include "Style/header.php"; ?>

<section id="edit-permissions" class="edit-permissions">

    <h1 style="color: rgb(199, 74, 74);">Order Management</h1>

<!--Order Table-->
    <table id="user-permissions-table">
        <thead>
            <tr>
                <th>Order Time</th>
                <th>Order ID</th>
                <th>Customer Email</th>
                <th>Address</th>
                <th>Ordered Items</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Update Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['order_time']) ?></td>
                        <td><?= htmlspecialchars($row['order_id']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= htmlspecialchars($row['ordered_products']) ?></td>
                        <td><span>&#8362; </span><?= htmlspecialchars($row['payment']) ?></td>
                        <td>
                            <?php 
                            // switch case statement for status drop down list
                            switch ($row['status']) {
                                case 0:
                                    echo 'Pending Payment';
                                    break;
                                case 1:
                                    echo 'Paid';
                                    break;
                                case 2:
                                    echo 'In Progress';
                                    break;
                                case 3:
                                    echo 'Order Ready';
                                    break;
                                case 4:
                                    echo 'Out For Delivery';
                                    break;
                                case 5:
                                    echo 'Completed';
                                    break;
                                case 6:
                                    echo 'Canceled';
                                    break;  
                                default:
                                    echo 'Unknown';
                                    break;
                            }
                            ?>
                        </td>
                        <td>     
                        <!--drop down list for admin to set order status-->
                                <form method="POST" action="admin_orders.php" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($row['order_id']) ?>">
                                    <select name="status" class="coupon-code-dropdown" required>
                                        <option value="0" <?= $row['status'] == 0 ? 'selected' : '' ?>>Pending Payment</option>
                                        <option value="1" <?= $row['status'] == 1 ? 'selected' : '' ?>>Paid</option>
                                        <option value="2" <?= $row['status'] == 2 ? 'selected' : '' ?>>In Progress</option>
                                        <option value="3" <?= $row['status'] == 3 ? 'selected' : '' ?>>Order Ready</option>
                                        <option value="4" <?= $row['status'] == 4 ? 'selected' : '' ?>>Out For Delivery</option>
                                        <option value="5" <?= $row['status'] == 5 ? 'selected' : '' ?>>Completed</option>
                                        <option value="6" <?= $row['status'] == 6 ? 'selected' : '' ?>>Canceled</option>
                                    </select>
                                            <button type="submit" class="status-btn" title="Update Status">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                </form>
                            
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No Orders found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

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

</section>

<?php include "Style/footer.php"; ?>

<?php
if (isset($message)) {
    echo "<script>alert('$message');</script>";
}
?>
