<?php
    require __DIR__.'/../../vendor/autoload.php';

    // Bootstrap your Laravel application
    $app = require_once __DIR__.'/../../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    use App\Models\UserSubscription;
	// echo "<b>From Resposne Page</b>" . "<br /><br />";
	// echo "Payment ID: " . $_POST["paymentid"] . "<br />";
	// echo "Track ID: " . $_POST["trackid"] . "<br />";
	// echo "Amount: " . $_POST["amt"] . "<br />";
	// echo "UDF 1: " . $_POST["udf1"] . "<br />";
	// echo "UDF 2: " . $_POST["udf2"] . "<br />";
	// echo "UDF 3: " . $_POST["udf3"] . "<br />";
	// echo "UDF 4: " . $_POST["udf4"] . "<br />";
	// echo "UDF 5: " . $_POST["udf5"] . "<br />";
	// echo "Error Text: " . $_POST["ErrorText"] . "<br />";
    $trackID =  $_POST["trackid"];
    $paymentID =  $_POST["paymentid"];
    UserSubscription::where('id',$trackID)->update([
        'payment_id' => $paymentID,
        'status' => 'failed'
]);
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
        background-color: #f5f5f5;
    }

    .failed-container {
        text-align: center;
        padding: 40px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .failed-icon {
        font-size: 64px;
        color: #f44336;
        margin-bottom: 20px;
    }

    .failed-message {
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
    }

    .back-to-site {
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 4px;
        background-color: #f44336;
        color: #fff;
        transition: background-color 0.3s ease;
    }

    .back-to-site:hover {
        background-color: #d32f2f;
    }
    </style>
</head>

<body>
    <div class="failed-container">
        <div class="failed-icon">&#x2717;</div>
        <div class="payment-details">
            <p>Payment ID: <?php echo $_POST["paymentid"]; ?></p>
            <p>Amount : <?php echo $_POST["amt"]; ?></p>
        </div>
        <div class="failed-message">Payment Failed!</div>
        <a href="https://admin.fitgate.live/failed" class="back-to-site">Back to App</a>
    </div>
</body>

</html>