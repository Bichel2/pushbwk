<?php
      function sendStkPush() {
        $token = getAccessToken();
        $timestamp = date('YmdHis'); 
        
        $shortCode = " 4140577"; //sandbox -174379
        $passkey = "3cf6e2fff13ba64211ff8e1e83f111630f0ad55450b70732404d629cd4d9a1bf";
        
        $stk_password = base64_encode($shortCode . $passkey . $timestamp);
        
       
        
        $url = "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
        
        $headers = [
          'Authorization: Bearer ' . $token,
          'Content-Type: application/json'
        ];
        
        $requestBody = array(
          "BusinessShortCode" => $shortCode,
          "Password" => $stk_password,
          "Timestamp" => $timestamp,
          "TransactionType" => "CustomerPayBillOnline", //till "CustomerBuyGoodsOnline"
          "Amount" => "10",
          "PartyA" => "254708374149",
          "PartyB" => $shortCode,
          "PhoneNumber" => "254708374149",
          "CallBackURL" => "https://bichel2.github.io/Bitec-/login.html",
          "AccountReference" => "account",
          "TransactionDesc" => "test"
        );
        
        try {
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $response = curl_exec($ch);
          curl_close($ch);
          echo $response;
          return $response;
        } catch (Exception $e) {
          echo 'Error: ',  $e->getMessage(), "
";
        }
      }
      ?>