<?php
/**
 * File Name: applyCoupon.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2024-12-25
 * Last Modified: 2024-12-30
 * Description:
 * This page handles the application of a coupon code for an order. The admin or user can submit a coupon 
 * code along with the total price of the order. The system performs the following tasks:
 *  - Validates the coupon code and checks if it exists in the database.
 *  - Ensures the coupon is still valid by checking the expiration date.
 *  - Calculates the discount amount based on the coupon's discount percentage and applies it to the total price.
 *  - Returns the discount amount and the final price after applying the discount.
 * 
 * If any errors occur (such as invalid coupon code, expired coupon, or invalid total price), an error message is returned.
 * 
 * The response is returned in JSON format, providing a clear success or failure message along with the applicable data (discount amount and final price).
 * 
 */

// require the configure the connection to db
require_once 'db_con.php';

header('Content-Type: application/json');

// check if we get POST from the front
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // check if inputs are empty
    if (!isset($input['coupon_code'], $input['total_price'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        exit;
    }

    // define vars for coupon code and total price
    $couponCode = trim($input['coupon_code']);
    $totalPrice = floatval($input['total_price']);

    if (empty($couponCode) || $totalPrice <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid coupon or total price.']);
        exit;
    }

    try {
        $conn = connectToDatabase();

        // Check if the coupon exists and is still valid
        $stmt = $conn->prepare("SELECT discount, end_date FROM coupons WHERE coupon_code = ?");
        $stmt->bind_param('s', $couponCode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired coupon code.']);
            exit;
        }

        $row = $result->fetch_assoc();
        $discount = floatval($row['discount']);
        $endDate = $row['end_date'];

        // Check if the coupon is still valid
        if (new DateTime() > new DateTime($endDate)) {
            echo json_encode(['success' => false, 'message' => 'This coupon has expired.']);
            exit;
        }

        // Calculate the discount amount and final price
        $discountAmount = ($discount / 100) * $totalPrice;
        $finalPrice = $totalPrice - $discountAmount;

        echo json_encode([
            'success' => true,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error processing the coupon.']);
    } finally {
        $conn->close();
    }
}
