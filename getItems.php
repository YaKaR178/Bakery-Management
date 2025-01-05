<?php
/**
 * File Name: getItems.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2025-01-01
 * Last Modified: 2025-01-01
 * Description:
 * This script retrieves a list of items from the bakery database and returns the data in JSON format. 
 * It is designed to serve as an API endpoint for fetching item details, which include SKU, name, image, and price.
 * 
 * Features:
 * - **Database Query**: Executes a query to fetch item details from the `items` table.
 * - **JSON Response**:
 *   - On success: Returns an array of items with their attributes (`SKU`, `item_name`, `image`, and `price`).
 *   - On failure: Returns an error message with a `success: false` status.
 * - **Error Handling**: Uses a `try-catch` block to handle exceptions and output an appropriate JSON error response.
 * - **Content-Type Header**: Ensures the response is properly formatted as JSON by setting the `Content-Type` header.
 * 
 * Usage:
 * - Include the database connection script (`db_con.php`) to establish a connection to the database.
 * - Call this script from a client-side application to fetch item data for display or processing.
 * 
 * Notes:
 * - The returned JSON structure includes:
 *   - `success`: Boolean indicating the success of the operation.
 *   - `items`: Array of item objects (on success).
 *   - `error`: Error message (on failure).
 * - Ensure proper database connection parameters in `db_con.php` and secure the API from unauthorized access.
 */
// Include DB connection
require 'db_con.php';
$conn = connectToDatabase();

header('Content-Type: application/json');

try {
    $query = "SELECT SKU, item_name, image, price FROM items";
    $result = $conn->query($query);

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    echo json_encode(['success' => true, 'items' => $items]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
