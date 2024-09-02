<?php
date_default_timezone_set('Asia/Kolkata'); // Set your local time zone

$validation_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    // Connection details for Banking database
    $bankingServername = "localhost";
    $bankingUsername = "your username"; 
    $bankingPassword = "your password"; // Change pass
    $bankingDbname = "Banking";

    // Database connection details for Trains database
    $trainsServername = "localhost";
    $trainsUsername = "your username"; 
    $trainsPassword = "your password"; // Change pass
    $trainsDbname = "Train";

    if (isset($_POST['action']) || isset($_GET['action'])) {
        $action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];

        if ($action === 'cancel') {
            // Connect to Trains database
            $connTrains = mysqli_connect($trainsServername, $trainsUsername, $trainsPassword, $trainsDbname);

            if (!$connTrains) {
                die("Connection to Trains database failed: " . mysqli_connect_error());
            }

            $sql_cancel = "UPDATE Tickets SET PayStatus = 'CANCELLED', TicketTime = NOW() ORDER BY SNo DESC LIMIT 1";
            $result_cancel = mysqli_query($connTrains, $sql_cancel);
            if ($result_cancel) {
                echo '<script>alert("Payment cancelled"); window.location.href = "home.html";</script>';
                exit();
            } else {
                $validation_error = 'Database update query execution failed: ' . mysqli_error($connTrains);
            }

            mysqli_close($connTrains);
        }

        if ($action === 'proceed') {
            if (isset($_POST['upi-id']) && isset($_POST['security-pin'])) {
                $upiID = $_POST['upi-id'];
                $securityPin = $_POST['security-pin'];
                $pattern = '/^[a-zA-Z0-9.\-_]{1,}@([a-zA-Z0-9]{1,})([a-zA-Z]{2,})$/';

                // Validate format on server-side
                if (!preg_match($pattern, $upiID)) {
                    $validation_error = 'UPI ID must be in the format username@provider and be no longer than 50 characters.';
                } else {
                    // Connect to Banking database
                    $connBanking = mysqli_connect($bankingServername, $bankingUsername, $bankingPassword, $bankingDbname);

                    if (!$connBanking) {
                        die("Connection to Banking database failed: " . mysqli_connect_error());
                    }

                    // Check if the UPI ID number and pin match in the Banking database
                    $sql_check_upi = "SELECT * FROM Accounts WHERE UpiID = ? AND SecurityPin = ?";
                    $stmt_check_upi = mysqli_prepare($connBanking, $sql_check_upi);
                    mysqli_stmt_bind_param($stmt_check_upi, "ss", $upiID, $securityPin);
                    mysqli_stmt_execute($stmt_check_upi);
                    $result_check_upi = mysqli_stmt_get_result($stmt_check_upi);

                    if (mysqli_num_rows($result_check_upi) > 0) {
                        // UPI ID and pin match, proceed to next steps
                        $account = mysqli_fetch_assoc($result_check_upi);
                        $accountBalance = $account['Balance'];
                        $accountHolderName = $account['HolderName'];
                        $accountNumber = $account['AccNo'];

                        mysqli_stmt_close($stmt_check_upi);
                        mysqli_close($connBanking);

                        // Connect to Trains database
                        $connTrains = mysqli_connect($trainsServername, $trainsUsername, $trainsPassword, $trainsDbname);

                        if (!$connTrains) {
                            die("Connection to Trains database failed: " . mysqli_connect_error());
                        }

                        // Fetch the last ticket price
                        $sql_last_row = "SELECT * FROM Tickets ORDER BY SNo DESC LIMIT 1";
                        $result_last_row = mysqli_query($connTrains, $sql_last_row);

                        if ($result_last_row && mysqli_num_rows($result_last_row) > 0) {
                            $last_row = mysqli_fetch_assoc($result_last_row);
                            $ticketID = $last_row['TicketID'];
                            $ticketPrice = $last_row['Price'];

                            if ($accountBalance >= $ticketPrice) {
                                // Deduct the ticket price from the account balance
                                $newBalance = $accountBalance - $ticketPrice;

                                // Connect to Banking database again to update the balance and insert transaction
                                $connBanking = mysqli_connect($bankingServername, $bankingUsername, $bankingPassword, $bankingDbname);

                                if (!$connBanking) {
                                    die("Connection to Banking database failed: " . mysqli_connect_error());
                                }

                                $sql_update_balance = "UPDATE Accounts SET Balance = ? WHERE UpiID = ?";
                                $stmt_update_balance = mysqli_prepare($connBanking, $sql_update_balance);
                                mysqli_stmt_bind_param($stmt_update_balance, "ds", $newBalance, $upiID);
                                mysqli_stmt_execute($stmt_update_balance);
                                mysqli_stmt_close($stmt_update_balance);

                                // Insert the transaction details into the Transactions table
                                $transactionID = substr(uniqid('TXN'), 0, 15); // Ensure the TransactionID is not longer than 15 characters
                                $paymentMode = 'UPI ID';
                                $paymentID = $upiID;
                                $date = date('Y-m-d');
                                $time = date('H:i:s'); // Get the current time in the server's timezone
                                $amount = $ticketPrice;

                                $sql_insert_transaction = "INSERT INTO Transactions (TransactionID, FromAcc, HolderName, PaymentMode, PaymentID, Date, Time, Amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                                $stmt_insert_transaction = mysqli_prepare($connBanking, $sql_insert_transaction);
                                mysqli_stmt_bind_param($stmt_insert_transaction, "sssssssd", $transactionID, $accountNumber, $accountHolderName, $paymentMode, $paymentID, $date, $time, $amount);
                                mysqli_stmt_execute($stmt_insert_transaction);
                                mysqli_stmt_close($stmt_insert_transaction);

                                mysqli_close($connBanking);

                                // Update the Tickets table
                                $sql_update = "UPDATE Tickets SET PayStatus = ?, PayMode = ?, PayID = ?, TicketTime = NOW() WHERE TicketID = ?";
                                $stmt_update = mysqli_prepare($connTrains, $sql_update);
                                if ($stmt_update) {
                                    $payStatus = 'SUCCESS';
                                    $payMode = 'UPI ID';
                                    $payID = $upiID;

                                    mysqli_stmt_bind_param($stmt_update, "ssss", $payStatus, $payMode, $payID, $ticketID);
                                    if (mysqli_stmt_execute($stmt_update)) {
                                        header("Location: tkt.php"); // Replace with your success page URL
                                        exit();
                                    } else {
                                        $validation_error = 'Database update query execution failed: ' . mysqli_stmt_error($stmt_update);
                                    }
                                    mysqli_stmt_close($stmt_update);
                                } else {
                                    $validation_error = 'Database update query preparation failed: ' . mysqli_error($connTrains);
                                }
                            } else {
                                $validation_error = 'Insufficient balance to complete the transaction.';
                            }
                        } else {
                            $validation_error = 'No tickets found in the database.';
                        }

                        mysqli_close($connTrains);
                    } else {
                        $validation_error = 'Invalid UPI ID number or pin.';
                    }

                    mysqli_close($connBanking);
                }
            } else {
                $validation_error = 'UPI ID number and pin are required.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .payment-container {
            border: 2px solid #000;
            padding: 30px;
            border-radius: 10px;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="payment-container text-center">
        <h4><b>Pay using UPI ID</b></h4>
        <?php if ($validation_error): ?>
            <div class="alert alert-danger"><?php echo $validation_error; ?></div>
        <?php endif; ?>
        <form id="payment-form" action="upi.php" method="post">
            <div class="form-group">
                <label for="upi-id">Enter UPI ID :</label>
                <input type="text" class="form-control <?php echo !empty($validation_error) ? 'is-invalid' : ''; ?>" id="upi-id" name="upi-id" maxlength="75" value="<?php echo isset($_POST['upi-id']) ? htmlspecialchars($_POST['upi-id']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="security-pin">Enter Security PIN:</label>
                <input type="password" class="form-control <?php echo !empty($validation_error) ? 'is-invalid' : ''; ?>" id="security-pin" name="security-pin" pattern="\d{4}" maxlength="4" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" name="action" value="proceed">Proceed to Pay</button>
                <button type="button" class="btn btn-secondary" onclick="confirmCancel()">Cancel Payment</button>
            </div>
        </form>
    </div>
    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function confirmCancel() {
            if (confirm('Are you sure you want to cancel the payment?')) {
                document.getElementById('payment-form').action = 'upi.php?action=cancel';
                document.getElementById('payment-form').submit();
            }
        }
    </script>
</body>
</html>
