
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
