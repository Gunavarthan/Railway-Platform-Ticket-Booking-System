<?php
// Create connection
$con = mysqli_connect("localhost", "your username", "your password", "train");
// Check connection
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to fetch the latest ticket price
function getTicketPrice($conn) {
    // Fetch the latest ticket price based on the highest SNo
    $sql = "SELECT Price FROM Tickets ORDER BY SNo DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $price = $row['Price'];
    } else {
        $price = 0; // Default value if no records are found
    }

    // Free result set
    mysqli_free_result($result);

    // Return formatted price
    return number_format($price, 2);
}

if (isset($_POST['cancel']) && $_POST['cancel'] === 'true') {
    // Update the PayStatus to 'CANCELLED' for the latest ticket
    $sql = "UPDATE Tickets SET PayStatus = 'CANCELLED', TicketTime=CURTIME() WHERE SNo = (SELECT SNo FROM (SELECT SNo FROM Tickets ORDER BY SNo DESC LIMIT 1) AS temp)";
   
    if (mysqli_query($conn, $sql)) {
        echo "Payment status updated successfully";
    } else {
        echo "Error updating payment status: " . mysqli_error($conn);
    }

    // Redirect to home page or display a success message
    header("Location: home.html");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        .red-bg {
            background-color: red;
            color: white;
            text-align: center;
            padding: 10px 0;
        }
        .price {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }
        .center-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            border: 2px solid black;
            padding: 20px;
            max-width: 600px;
        }
        .btn-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 80%;
        }
        .price-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            flex-direction: column;
        }
        .row {
            display: flex;
            align-items: center;
        }
        .cancel-btn-container {
            text-align: center;
            margin-top: 20px;
        }
        .cancel-btn {
            width: 100%;
            font-size: 18px;
        }
    </style>
</head>
<body>
   
    <div class="center-container">
        <div class="container">
            <div class="red-bg">
                <h2>Please select payment method!!!</h2>
            </div><br>
            <div class="row">
                <div class="col-md-8">
                    <div class="btn-container">
                        <form action="smt.php" method="post" class="form-group">
                            <button type="submit" class="btn btn-primary btn-block" name="submit">Railway Smart Card</button>
                        </form>
                        <form action="upi.php" method="post" class="form-group">
                            <button type="submit" class="btn btn-primary btn-block" name="submit">UPI Banking</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="price-container">
                        <center><div class="price">
                            Price: ₹ <?php echo getTicketPrice($conn); ?>
                        </div></center>
                    </div>
                </div>
            </div>
            <div class="cancel-btn-container">
                <form action="" method="post" onsubmit="return confirmCancellation();">
                    <input type="hidden" name="cancel" value="true">
                    <button type="submit" class="btn btn-danger cancel-btn">Cancel Payment</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        function confirmCancellation() {
            if (confirm("Do you want to cancel for sure?")) {
                alert("Payment canceled");
                return true;
            }
            return false;
        }
    </script>
</body>
</html>

<?php
// Close connection at the end of the script
mysqli_close($conn);
?>
