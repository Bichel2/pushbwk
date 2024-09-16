<?php
// Capture the callback data sent by Safaricom
$callbackData = file_get_contents('php://input');

// Log the callback response for debugging purposes
$logFile = 'mpesa_callback_response.log';
file_put_contents($logFile, $callbackData . PHP_EOL, FILE_APPEND);

// Decode the JSON data
$jsonData = json_decode($callbackData, true);

// Process the callback data as needed
if (isset($jsonData['Body']['stkCallback']['ResultCode'])) {
    $resultCode = $jsonData['Body']['stkCallback']['ResultCode'];
    $resultDesc = $jsonData['Body']['stkCallback']['ResultDesc'];
    $amount = $jsonData['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];

    // Do something with the callback data, e.g., update the payment status in the database
    if ($resultCode == 0) {
        // Successful transaction
        // Update your database with successful payment
    } else {
        // Transaction failed
        // Log or handle the failure
    }
}
