<?php
	require('BENEFIT.php');
	require __DIR__.'/../../vendor/autoload.php';

	// Bootstrap your Laravel application
	$app = require_once __DIR__.'/../../bootstrap/app.php';
	$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
	use App\Models\UserSubscription;
	use App\Helper\AppHelper;
	use App\Models\User;
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
		
	// echo $paymentID;
	// echo $result;
	// echo $responseCode;
	// echo $transactionID;
	// echo $referenceID;
	// echo $trackID;
	// echo $amount;
	// echo $UDF1;
	// echo $UDF2;
	// echo $UDF3;
	// echo $UDF4;
	// echo $UDF5;
	// echo $authCode;
	// echo $postDate;
	// echo $errorCode;
	// echo $errorText;
	$trackID = $trackID - 600;
	UserSubscription::where('id',$trackID)->update([
			'payment_id' => $paymentID,
			'status' => 'success'
	]);
	Helper::invoiceMailSend($trackID);
	$usersub =  UserSubscription::with(['user','gym'])
           ->where('id',$trackID)
           ->first();
       $message = $usersub->user->name." have just subscribed to ".$usersub->gym->facility_name;
       $admin_tokens = User::whereIn('type',['admin','staff'])
           ->whereNotNull('fcm_token')
           ->pluck('fcm_token')->toArray();
       if(!empty($admin_tokens)){
           AppHelper::sendPushNotifications($admin_tokens,"User Subscription",$message);
       }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
        overflow-x: hidden;
        /* Hide horizontal scrollbar */
    }

    .success-container {
        text-align: center;
        padding: 20px;
    }

    .msg {
        padding: 13px 55px;
    }

    .imag img {
        height: 450px;
        width: 450px;
        display: block;
        margin: 51px auto;
    }

    .success-message {
        font-size: 65px;
        margin-top: 20px;
        color: #000;
        text-align: center;
        font-family: Poppins;
        font-style: normal;
        font-weight: 600;
        line-height: 60px;
        text-transform: capitalize;
    }

    .payment-details {
        margin: 10px 88px 15px 88px;
        border-radius: 31px;
        padding: 40px;
        font-size: xxx-large;
        border: 1px dashed #E53D00;
        background: rgba(229, 61, 0, 0.08);
    }

    .button {
        margin-top: 20px;
    }

    .back-to-site {
        display: inline-block;
        padding: 40px 87px;
        background-color: #E53D00;
        color: #fff;
        text-decoration: none;
        font-size: 54px;
        border-radius: 22px;
    }

    .msg p {
        color: #566D6F;
        text-align: center;
        font-family: Poppins;
        font-size: 40px;
        font-style: normal;
        font-weight: 300;
        line-height: 59px !important;
        margin: 50px;
        line-height: 26px;
        text-transform: capitalize;
    }

    .contact {
        padding-top: 50px;
        padding-bottom: 50px;
    }

    .contact p {
        color: #566D6F;
        font-size: 40px;
    }
    </style>
</head>

<body>
    <div class="success-container">
        <div class="imag">
            <img src="payment.png" alt="Icon">
        </div>
        <div class="success-message">your payment was Successful</div>
        <div class="msg">
            <p>thank you for your payment. we will be in contact with more details shortly</p>
        </div>
        <div class="payment-details">
            <?php echo $paymentID; ?>
        </div>
        <div class="contact">
            <p style="color:#566D6F">Do not hesitate to contact us</p>
            <svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 76 76" fill="none">
                <a href="https://wa.me/97337049009" target="_blank">
                    <g clip-path="url(#clip0_231_91)">
                        <path
                            d="M62.7 0H13.3C5.95461 0 0 5.95461 0 13.3V62.7C0 70.0454 5.95461 76 13.3 76H62.7C70.0454 76 76 70.0454 76 62.7V13.3C76 5.95461 70.0454 0 62.7 0Z"
                            fill="url(#paint0_linear_231_91)" />
                        <path
                            d="M32.5613 25.3888L34.9363 31.8369C34.9938 31.9812 35.0131 32.1379 34.9923 32.2919C34.9714 32.4458 34.9113 32.5918 34.8175 32.7156C34.3078 33.4549 33.7185 34.1358 33.06 34.7463C32.8685 34.8966 32.7421 35.1147 32.7067 35.3555C32.6714 35.5964 32.7298 35.8416 32.87 36.0406C34.3306 38 38.1188 42.75 43.0469 44.4481C43.2368 44.5109 43.4417 44.5113 43.6318 44.4493C43.8219 44.3874 43.9873 44.2663 44.1038 44.1038L46.075 41.4794C46.2095 41.3019 46.4022 41.1773 46.6192 41.1274C46.8363 41.0774 47.064 41.1054 47.2625 41.2063L53.4375 44.2938C53.6522 44.3949 53.8206 44.5737 53.9087 44.794C53.9969 45.0144 53.9982 45.26 53.9125 45.4813C53.2356 47.405 51.11 52.0956 46.5619 51.3356C40.6144 50.4231 35.205 47.37 31.35 42.75C27.6213 38.095 20.2588 26.125 31.5756 24.7713C31.7851 24.7504 31.9956 24.7998 32.174 24.9115C32.3524 25.0233 32.4886 25.1912 32.5613 25.3888Z"
                            fill="white" />
                        <path
                            d="M39.8999 65.0751C34.9319 65.082 30.0469 63.8014 25.7212 61.3582L11.4712 65.0157L16.7081 53.2476C13.5425 48.7954 11.8522 43.4629 11.8749 38.0001C11.8749 23.0732 24.4506 10.925 39.8999 10.925C55.3493 10.925 67.9249 23.0732 67.9249 38.0001C67.9249 52.9269 55.3493 65.0751 39.8999 65.0751ZM26.4693 56.2519L27.3362 56.7863C31.1123 59.1169 35.4625 60.3504 39.8999 60.3488C52.7368 60.3488 63.1749 50.3382 63.1749 38.0238C63.1749 25.7094 52.7368 15.675 39.8999 15.675C27.0631 15.675 16.6249 25.6857 16.6249 38.0001C16.6302 42.8985 18.3058 47.6486 21.3749 51.4663L22.2656 52.5944L17.9787 59.8619L26.4693 56.2519Z"
                            fill="white" />
                    </g>
                    <defs>
                        <linearGradient id="paint0_linear_231_91" x1="38" y1="4.75" x2="38" y2="76.9619"
                            gradientUnits="userSpaceOnUse">
                            <stop stop-color="#1DF47C" />
                            <stop offset="0.31" stop-color="#12DF63" />
                            <stop offset="0.75" stop-color="#05C443" />
                            <stop offset="1" stop-color="#00BA37" />
                        </linearGradient>
                        <clipPath id="clip0_231_91">
                            <rect width="76" height="76" fill="white" />
                        </clipPath>
                    </defs>
                </a>
            </svg>
        </div>
        <div class="button"><a href="https://www.admin.fitgate.live/success" class="back-to-site">Back to Home</a></div>
    </div>
</body>

</html>