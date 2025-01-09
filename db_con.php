<?php
/**
 * File Name: db_con.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2025-01-01
 * Last Modified: 2025-01-01
 * Description:
 * This function establishes a connection to the **BakeryDB** database, which stores data related to the "Baking from the Heart" bakery system.
 * 
 * Features:
 * - **Connection Handling**:
 *   - Uses PHP's `mysqli` class to create a database connection.
 *   - Checks for connection errors and terminates the script with an error message if the connection fails.
 * 
 * Usage:
 * - Call `connectToDatabase()` to retrieve a `mysqli` object for interacting with the `BakeryDB`.
 * - The returned connection should be closed after usage to free resources.
 */
function connectToDatabase() {
    $servername = "SERVER_NAME";
    $dbname = "DB_NAME";
    $dbusername = "DB_USERNAME";
    $dbpassword = "DB_PASSWORD";

    // Create a connection
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    // Check connection
    if ($conn->connect_errno) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
