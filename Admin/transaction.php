<?php
    $con = mysqli_connect("localhost", "your username", "your password", "train");
    $transactionSearchPerformed = false;
    $totalTransactions = 0;
    $totalAmount = 0.0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo.png">
    <link rel="stylesheet" href="css/style.css">
    <title>Transaction</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .total-info {
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }
        .cancelled {
            background-color: #f8d7da;
            color: #721c24; 
        }
        .cancelled td {
            color: #721c24; 
        }
        .info-message {
            text-align: center;
            font-size: 18px;
            color: #d97716;
            margin: 20px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <span class="title">My Train</span>
        <a href="search.php">Search</a>
        <a href="asearch.php">Advanced Search</a>
        <a href="statistics.php">Statistics</a>
        <a href="#" class="active">Transaction</a>
    </div>

    <div class="info-message">
        <p>ⓘPlease enter the start and end dates below to view all transactions within that date range.</p>
    </div>

    <div class="search-container">
        <form action="transaction.php" method="post">
            <input type="date" name="date_from" required>
            <input type="date" name="date_to" required>
            <button type="submit" name="transaction_search">Search Transactions</button>
        </form>
    </div>

    <?php
        if (isset($_POST["transaction_search"])) {
            $date_from = $_POST["date_from"];
            $date_to = $_POST["date_to"];

            $transaction_query = "SELECT * FROM tickets WHERE TicketDate BETWEEN '$date_from' AND '$date_to'";
            $transaction_result = mysqli_query($con, $transaction_query);
            $transactionSearchPerformed = true;

            // Display the date range message after search
            echo '<div class="info-message">';
            echo 'Showing transactions from ' . htmlspecialchars($date_from) . ' to ' . htmlspecialchars($date_to) . '.';
            echo '</div>';

            if (isset($transaction_result) && mysqli_num_rows($transaction_result) > 0) {
                echo '<table>';
                echo '<tr><th>Pay ID</th><th>Ticket ID</th><th>Pay Mode</th><th>Date</th><th>Time</th><th>Price</th><th>PNR</th><th>Status</th></tr>';
                while ($row = mysqli_fetch_assoc($transaction_result)) {
                    $rowClass = ($row['PayStatus'] === 'CANCELLED') ? 'class="cancelled"' : '';
                    
                    echo '<tr ' . $rowClass . '>';
                    echo '<td>' . $row['PayID'] . '</td>';
                    echo '<td>' . $row['TicketID'] . '</td>';
                    echo '<td>' . $row['PayMode'] . '</td>';
                    echo '<td>' . $row['TicketDate'] . '</td>';
                    echo '<td>' . $row['TicketTime'] . '</td>';
                    echo '<td>₹' . $row['Price'] . '</td>';
                    echo '<td>' . $row['PNR'] . '</td>';
                    echo '<td>' . $row['PayStatus'] . '</td>';
                    echo '</tr>';

                    $totalTransactions++;
                    if ($row['PayStatus'] !== 'Cancelled') {
                        $totalAmount += $row['Price'];
                    }
                }
                echo '</table>';

                echo '<div class="total-info">';
                echo 'Total Transactions: ' . $totalTransactions . ' &nbsp; &nbsp; &nbsp; ';
                echo 'Total Amount Collected: ₹' . $totalAmount;
                echo '</div>';
            } else {
                echo '<div style="color:red;text-align: center; font-size: 24px; margin-top: 20px;">No results found.</div>';
            }
        }
    ?>
</body>
</html>

