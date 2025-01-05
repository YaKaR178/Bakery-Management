<?php
/**
 * File Name: newsletter.php
 * Author: Eliasaf Yakar, Niv Zukerman, and Nissan Yaar
 * Created On: 2025-01-05
 * Last Modified: 2025-01-05
 * Description: Handles newsletter subscriptions for the bakery, validating emails and avoiding duplicates.
 */

require_once 'db_con.php';
$conn = connectToDatabase();

$message = ""; // To store messages for the user

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['mail'])) {
        $subscriberMail = trim($_POST['mail']);

        // Validate email format
        if (filter_var($subscriberMail, FILTER_VALIDATE_EMAIL)) {
            // Check if email already exists in the database
            $stmt = $conn->prepare("SELECT COUNT(*) FROM newsletter WHERE mail = ?");
            $stmt->bind_param("s", $subscriberMail);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                $message = "This email is already subscribed to the newsletter";
            } else {
                // Insert email into database
                $stmt = $conn->prepare("INSERT INTO newsletter(mail) VALUES (?)");
                $stmt->bind_param("s", $subscriberMail);
                if ($stmt->execute()) {
                    $message = "Thank you for subscribing to our newsletter!";
                } else {
                    $message = "An error occurred. Please try again later";
                }
                $stmt->close();
            }
        } else {
            $message = "Please enter a valid email address";
        }
    }
}
?>

<!-- Newsletter -->
<section id="newsletter" class="section-p1">
    <div class="newstext">
        <h4>Sign Up for Our Newsletter</h4>
        <p>Get notified about our latest products and <span>special offers</span></p>
    </div>

    <form action="newsletter.php" method="POST">
        <div class="form">
            <input type="text" name="mail" placeholder="Your email address" required>
            <button class="normal" type="submit">Sign Up</button>
        </div>
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </form>
</section>

<style>
.message {
    color: rgb(199, 74, 78);
    font-size: 14px;
    margin-top: 10px;
}
</style>
