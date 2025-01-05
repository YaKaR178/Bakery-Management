<?php
/**
 * File Name: logout.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2025-01-01
 * Last Modified: 2025-01-01
 * Description:
 * This PHP script handles the user logout process by clearing all session data and redirecting the user to the homepage. It ensures a secure and complete logout by removing session variables and destroying the session.

 * Features:
 * - **Session Management**:
 *   - Starts the session using `session_start()` if not already active.
 *   - Removes all session variables with `session_unset()`.
 *   - Destroys the session entirely using `session_destroy()`.
 * - **User Redirection**:
 *   - Redirects the user to the homepage (`index.php`) after the session is terminated.
 * - **Secure Logout**:
 *   - Ensures that no residual session data persists after logout, safeguarding user privacy.
 */
session_start();
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session
header("Location: index.php"); // Redirect to the homepage
exit();
?>
