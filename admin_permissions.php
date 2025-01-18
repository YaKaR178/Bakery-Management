<!--Header-->
<?php include_once 'Style/header.php'; ?>

<?php
/**
 * File Name: admin_permissions.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2024-12-23
 * Last Modified: 2024-12-30
 * Description: 
 * This page allows the admin to manage user permissions and view the list of users. 
 * The admin can perform the following actions:
 *  - Toggle user roles between regular user and admin.
 *  - Delete users from the system.
 *  - View a paginated list of users excluding the admin account.
 * 
 * Each user in the list includes the following details:
 *  - Username: The email or username of the user.
 *  - Status: Indicates if the user is an admin or a regular user.
 * 
 * The admin can change the user status to grant or revoke admin privileges, 
 * or delete the user entirely from the system. Actions are confirmed through 
 * a confirmation prompt to ensure intentional operations.
 * 
 */

 
// require the configure the connection to db
require_once 'db_con.php';
$conn = connectToDatabase();

// limitation for pagination
$limit = 10;

// define page and offset for pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// query for pagination
$resultCount = $conn->query("SELECT COUNT(*) AS total_users FROM users WHERE username NOT LIKE 'admin'");
$totalUsers = $resultCount->fetch_assoc()['total_users'];

// main query for users table
$result = $conn->query("SELECT username, is_admin FROM users WHERE username NOT LIKE 'admin' ORDER BY is_admin DESC, username LIMIT $limit OFFSET $offset");

// calculate the number of pages
$totalPages = ceil($totalUsers / $limit);

// check if we get POST from the front
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        $email = $conn->real_escape_string($_POST['username']);
        if ($_POST['action'] === "toggle_admin") {
            $isAdmin = intval($_POST['is_admin']);
            $newStatus = $isAdmin === 1 ? 0 : 1;
            $conn->query("UPDATE users SET is_admin=$newStatus WHERE username='$email'");
        } elseif ($_POST['action'] === "delete_user") {
            $conn->query("DELETE FROM users WHERE username='$email'");
        }
    }
}
?>

<!-- Edit User Permissions--> 
<section id="edit-permissions" class="edit-permissions">

    <h1 style="color: rgb(199, 74, 74);">User Permissions Management</h1>

    <!--User Management Table-->
    <table id="user-permissions-table">
        <thead>
            <tr>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= $row['is_admin'] ? 'Admin' : 'Regular' ?></td>
                        <td>
                            <!--Admin permission-->
                            <form method="POST" style="display:inline;" onsubmit="return confirmAction('toggle_admin', '<?= htmlspecialchars($row['username']) ?>')">
                                <input type="hidden" name="username" value="<?= htmlspecialchars($row['username']) ?>">
                                <input type="hidden" name="is_admin" value="<?= $row['is_admin'] ?>">
                                <input type="hidden" name="action" value="toggle_admin">
                                <button type="submit" class="toggle-btn" title="<?= $row['is_admin'] ? 'Remove Admin Permission' : 'Make Admin' ?>">
                                    <i class="fas fa-user-shield"></i> 
                                </button>
                            </form>

                            <!--Delete User-->
                            <form method="POST" style="display:inline;" onsubmit="return confirmAction('delete_user', '<?= htmlspecialchars($row['username']) ?>')">
                                <input type="hidden" name="username" value="<?= htmlspecialchars($row['username']) ?>">
                                <input type="hidden" name="action" value="delete_user">
                                <button type="submit" class="delete-btn" title="Delete User">
                                    <i class="fas fa-trash"></i> 
                                </button>
                            </form>
                        </td>
                    </tr>

                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No users found</td>
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



<?php include "Style/footer.php";?>

<?php
// message to admin after success/failure of process
if (isset($message)) {
    echo "<script>alert('$message');</script>";
}
?>

<script>

    // function for action button (delete/give permission)
    function confirmAction(actionType, username) {
        let message = '';
        if (actionType === 'delete_user') {
            message = `Are you sure you want to delete the user "${username}"? This action cannot be undone.`;
        } else if (actionType === 'toggle_admin') {
            message = `Are you sure you want to change admin permissions for the user "${username}"?`;
        }
        return confirm(message); 
    }
</script>

