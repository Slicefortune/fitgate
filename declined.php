<?php
	require('BENEFIT.php');
	require __DIR__.'/../../vendor/autoload.php';

	// Bootstrap your Laravel application
	$app = require_once __DIR__.'/../../bootstrap/app.php';
	$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
	use App\Models\UserSubscription;
	use App\Helper\Helper;
	
	$myObj = new iPayBenefitPipe();
		
	// modify the following to reflect your "Terminal Resourcekey"
	$myObj->setkey("33616418970833616418970833616418");   
	
	$trandata = "";
	$paymentID = "";
	$result = "";
	$responseCode = "";
	$transactionID = "";
	$referenceID = "";
	$trackID = "";
	$amount = "";
	$UDF1 = "";
	$UDF2 = "";
	$UDF3 = "";
	$UDF4 = "";
	$UDF5 = "";
	$authCode = "";
	$postDate = "";
	$errorCode = "";
	$errorText = "";
	
	$trandata = isset($_POST["trandata"]) ? $_POST["trandata"] : "";
	
	if ($trandata != "")
	{
		$myObj->settrandata($trandata);
		
		$returnValue =  $myObj->parseResponseTrandata();
		if ($returnValue == 1)
		{
			$paymentID = $myObj->getpaymentId();
			$result = $myObj->getresult();
			$responseCode = $myObj->getauthRespCode();
			$transactionID = $myObj->gettransId();
			$referenceID = $myObj->getref();
			$trackID = $myObj->gettrackId();
			$amount = $myObj->getamt();
			$UDF1 = $myObj->getudf1();
			$UDF2 = $myObj->getudf2();
			$UDF3 = $myObj->getudf3();
			$UDF4 = $myObj->getudf4();
			$UDF5 = $myObj->getudf5();
			$authCode = $myObj->getauthCode();
			$postDate = $myObj->gettranDate();
			$errorCode = $myObj->geterror();
			$errorText = $myObj->geterrorText();
		}
		else
		{
			$errorText = $myObj->geterrorText();
		}
	}
	else if (isset($_POST["ErrorText"]))
    {
        $paymentID = $_POST["paymentid"];
        $trackID = $_POST["Trackid"];
        $amount = $_POST["amt"];
        $UDF1 =  $_POST["UDF1"];
        $UDF2 =  $_POST["UDF2"];
        $UDF3 =  $_POST["UDF3"];
        $UDF4 =  $_POST["UDF4"];
        $UDF5 = $_POST["UDF5"];
        $errorText = $_POST["ErrorText"];
    }
    else
    {
        $errorText = "Unknown Exception";
    }
		
	// echo $paymentID.'=';
	// echo $result.'=';
	// echo $responseCode.'=';
	// echo $transactionID.'=';
	// echo $referenceID.'=';
	// echo $trackID.'=';
	// echo $amount.'=';
	// echo $UDF1.'=';
	// echo $UDF2.'=';
	// echo $UDF3.'=';
	// echo $UDF4.'=';
	// echo $UDF5.'=';
	// echo $authCode.'=';
	// echo $postDate.'=';
	// echo $errorCode.'=';
	// echo $errorText.'=';
	$trackID = (int)$trackID - 600;
	UserSubscription::where('id',$trackID)->update([
		'payment_id' => $paymentID,
		'status' => 'failed'
]);


	switch ($responseCode)
	{
		case "05":
			$response = "Please contact issuer";
			break;
		case "14":
			$response = "Invalid card number";
			break;
		case "33":
			$response = "Expired card";
			break;
		case "36":
			$response = "Restricted card";
			break;
		case "38":
			$response = "Allowable PIN tries exceeded";
			break;
		case "51":
			$response = "Insufficient funds";
			break;
		case "54":
			$response = "Expired card";
			break;
		case "55":
			$response = "Incorrect PIN";
			break;
		case "61":
			$response = "Exceeds withdrawal amount limit";
			break;
		case "62":
			$response = "Restricted Card";
			break;
		case "65":
			$response = "Exceeds withdrawal frequency limit";
			break;
		case "75":
			$response = "Allowable number PIN tries exceeded";
			break;
		case "76":
			$response = "Ineligible account";
			break;
		case "78":
			$response = "Refer to Issuer";
			break;
		case "91":
			$response = "Issuer is inoperative";
			break;
		default:
			// for unlisted values, please generate a proper user-friendly message
			$response = "Unable to process transaction temporarily. Try again later or try using another card.";
			break;
	}

	// Helper::invoiceMailSend($trackID);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Failed</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.5);
        /* Semi-transparent background */
    }

    .failed-container {
        text-align: center;
        padding: 56px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .failed-icon {
        /* Icon size */
        color: #f44336;
        font-size: 160px;
        margin-bottom: 20px;
    }

    .failed-message {
        font-size: 57px;
        padding-top: 60px;
        color: #333;
        margin-bottom: 20px;
    }

    .back-to-site {
        text-decoration: none;
        /* Larger button */
        background-color: #f44336;
        color: #fff;
        transition: background-color 0.3s ease;
        /* Larger button text */
        font-size: 57px;
        padding: 24px;
        border-radius: 23px;
    }

    .back-to-site:hover {
        background-color: #d32f2f;
    }

    .kd {
        font-size: 30px;
        /* Larger heading */
        font-weight: bold;
        /* Bold text */
        margin-bottom: 15px;
    }

    .payment-details {
        font-size: 44px;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <div class="failed-container">
        <div class="kd">Failed</div>
        <div class="failed-icon">&#x2717;</div>
        <div class="failed-message"><?php echo $response; ?></div>
        <div class="payment-details">
            <p>Payment ID: <?php echo $paymentID; ?></p>
        </div>
        <a href="https://www.admin.fitgate.live/failed" class="back-to-site">Back to App</a>
    </div>
</body>

</html>