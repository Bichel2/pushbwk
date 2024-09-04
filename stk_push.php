<?php
if(isset($_POST['submit'])){
    // Set the timezone to Nairobi (necessary for timestamp generation)
    date_default_timezone_set('Africa/Nairobi');

    // Safaricom API credentials
    $consumerKey = '4YwHi4pVqVCNKYzGiFXG6fS9P5gFz9KhvjYuLEde2ysyeM5U';
    $consumerSecret = '5wp0yrE3YlEGD6ihauNOgTsC3KtO9xiTdVSeSe16uUwz4FodJD2LWUHn61wTiQwb';
    
    // Function to generate the access token using consumer key and secret
    function generateAccessToken($consumerKey, $consumerSecret) {
        $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
        $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        // Setting headers including authorization and content type
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials, 'Content-Type:application/json'));
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); // Skip SSL verification for this sandbox request

        $curl_response = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Error handling for cURL execution
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            throw new Exception('CURL Error: ' . $error_msg);
        }

        curl_close($curl);

        $result = json_decode($curl_response);

        // Check for successful token generation
        if ($http_status != 200) {
            throw new Exception('Failed to generate access token. HTTP Status: ' . $http_status . ' Response: ' . $curl_response);
        }

        if (isset($result->access_token)) {
            return $result->access_token; // Return the access token
        } else {
            throw new Exception('Failed to generate access token. Response: ' . $curl_response);
        }
    }

    try {
        // Attempt to generate the access token
        $accessToken = generateAccessToken($consumerKey, $consumerSecret);
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }

    // M-PESA Business Shortcode and Passkey
    $BusinessShortCode = '174379';
    $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';  
    
    // Get user input for phone number and amount from the form
    $PartyA = $_POST['phonenumber']; // Phone number in the format 2547XXXXXXXX
    $AccountReference = 'Pio Spices East Africa'; // Reference for the transaction
    $TransactionDesc = 'Test Lipa Na Mpesa STK Push Initiation'; // Transaction description
    $Amount = $_POST['amount']; // Amount to be charged
    
    // Generate the timestamp for the transaction
    $Timestamp = date('YmdHis');    
    // Encode the password using BusinessShortCode, Passkey, and Timestamp
    $Password = base64_encode($BusinessShortCode.$Passkey.$Timestamp);

    // Headers for the STK push request
    $stkheader = [
        'Authorization: Bearer '.$accessToken, // Include the access token
        'Content-Type: application/json' // Ensure content is JSON
    ];

    // Prepare the data for the STK push request
    $curl_post_data = array(
        'BusinessShortCode' => $BusinessShortCode,
        'Password' => $Password,
        'Timestamp' => $Timestamp,
        'TransactionType' => 'CustomerPayBillOnline', // Define transaction type
        'Amount' => $Amount,
        'PartyA' => $PartyA, // The phone number sending the payment
        'PartyB' => $BusinessShortCode, // The paybill/till number receiving the payment
        'PhoneNumber' => $PartyA, // The phone number to receive the STK push
        'CallBackURL' => 'https://stk-push-php.herokuapp.com/callback.php', // URL to receive the callback
        'AccountReference' => $AccountReference, // Reference for the payment
        'TransactionDesc' => $TransactionDesc // Description of the transaction
    );

    // Convert the array to JSON for the request
    $data_string = json_encode($curl_post_data);

    // Initialize cURL for the STK push request
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
    curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

    // Execute the STK push request
    $response = curl_exec($curl);

    // Handle potential errors from cURL
    if (curl_errno($curl)) {
        die("CURL Error: " . curl_error($curl));
    }

    // Check the HTTP status of the response
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($http_status != 200) {
        die("Error: API request failed with status code " . $http_status . ". Response: " . $response);
    }

    curl_close($curl);

    // Output the response for debugging or success confirmation
    echo "STK Push initiated successfully! Response: " . $response;
} else {
    echo 'Error: Submit button not clicked.'; // Error handling if the form was not submitted
}



