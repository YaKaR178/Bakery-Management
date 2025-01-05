<?php
/**
 * File Name: paypal_payment.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2025-12-19
 * Last Modified: 2025-01-05
 * Description:
 * This PHP script integrates PayPal's payment gateway to process transactions. It handles creating a PayPal payment, setting up the necessary API context, and redirecting the user to PayPal for approval. The script uses the PayPal SDK for PHP.

 * Features:
 * - **PayPal API Integration**:
 *   - Uses the PayPal REST API via the PayPal SDK to handle payments.
 * - **Secure Payment Configuration**:
 *   - Sets up an `ApiContext` with client credentials for secure API access.
 * - **Dynamic Payment Handling**:
 *   - Validates and processes the payment amount and description from user input.
 *   - Supports transactions in ILS (Israeli New Shekel) currency.
 * - **Redirect Management**:
 *   - Redirects users to PayPal's approval URL for payment authorization.
 *   - Handles both successful and canceled payments via `returnUrl` and `cancelUrl`.
 * - **Error Handling**:
 *   - Displays detailed error messages for debugging during the development phase.
 */
session_start();
require 'vendor/autoload.php';

use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;


$clientId = 'AQs3vx67EeRqJ0DlgF69nW2zQwZ3n_NDV9y6_l_hyst3WfbuZ8OlPd2kXlK3qx6ngZhy-f3wZbpnmfsg';
$clientSecret = 'EAqW6OQAHElAILLwfZBRlvB1BcNcMxd45rfFk0Ynm5Uivgw7sojyYLSmyPtgLXdrVkFUkurstkUd349V';


$apiContext = new ApiContext(
    new OAuthTokenCredential(
        $clientId,
        $clientSecret
    )
);

// Validate and retrieve the payment amount from the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['total_price']) || !is_numeric($_POST['total_price'])) {
        die('Invalid payment amount.');
    }

    $paymentAmount = (float)$_POST['total_price'];
    $description = $_POST['description'];
} else {
    die('Invalid request method.');
}

// Define payment parameters
$amount = new Amount();
$amount->setCurrency('ILS');
$amount->setTotal(number_format($paymentAmount, 2, '.', ''));

$transaction = new Transaction();
$transaction->setDescription($description);
$transaction->setAmount($amount);

$payer = new Payer();
$payer->setPaymentMethod('paypal');

$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl('http://localhost:8000/payment_success.php');
$redirectUrls->setCancelUrl('http://localhost:8000/payment_cancel.php');

$payment = new Payment();
$payment->setIntent('sale');
$payment->setPayer($payer);
$payment->setTransactions([$transaction]);
$payment->setRedirectUrls($redirectUrls);

try {
    $payment->create($apiContext);

    
    foreach ($payment->getLinks() as $link) {
        if ($link->getRel() === 'approval_url') {
            header('Location: ' . $link->getHref());
            exit;
        }
    }
} catch (Exception $ex) {
    echo 'Error: ' . $ex->getMessage();
    exit;
}