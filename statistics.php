<?php
    $con = mysqli_connect("localhost", "root", "guna", "train");

    // Query to get platform number with maximum profit
    $max_platform_query = "
        SELECT platformnumber, SUM(price) as total_profit            
        FROM tickets
        GROUP BY platformnumber
        ORDER BY total_profit DESC
        LIMIT 1
    ";          //sums up the price and group by platformNumber and order in descending order limit 1 result the top of the list
    $max_platform_result = mysqli_query($con, $max_platform_query);
    $max_platform = mysqli_fetch_assoc($max_platform_result);

    // Query to get month with maximum profit
    $max_month_query = "
        SELECT DATE_FORMAT(ticketdate, '%Y-%m') as month, SUM(price) as total_profit
        FROM tickets
        GROUP BY month
        ORDER BY total_profit DESC
        LIMIT 1
    ";
    $max_month_result = mysqli_query($con, $max_month_query);
    $max_month = mysqli_fetch_assoc($max_month_result);

    if (isset($_POST["search"])) {
        $ticket_date = $_POST["ticket_date"];
        $platform_number = $_POST["platform_number"];
        
        $query = "SELECT * FROM tickets";
        
        $conditions = array();
        if (!empty($ticket_date)) {
            $conditions[] = "ticketdate = '$ticket_date'";
        }
        if (!empty($platform_number)) {
            $conditions[] = "platformnumber = $platform_number";
        }
        
        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(' AND ', $conditions);                //implode function just separates the elements of array with provided delimiter
        }

        $result = mysqli_query($con, $query);
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>My Train</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .statistics-container {
            margin: 20px;
            text-align: center;
            background-color: #007bff;
            color: white;
            padding: 20px;
        }
        </style>
</head>
<body>
<div class="navbar">
        <span class="title">My Train</span>
        <a href="search.php">Search</a>
        <a href="asearch.php">Advanced Search</a>
        <a href="#" class="active">Statistics</a>
    </div>

    <div class="search-container">
        <form action="statistics.php" method="post">
            <input type="date" name="ticket_date" placeholder="Enter Ticket Date">              <!--input for ticket date-->
            <input type="text" name="platform_number" placeholder="Enter Platform Number">      <!--input for platform number-->
            <button type="submit" name="search">Search</button>                                         
        </form>
    </div>
    
    <?php
        if (isset($result) && mysqli_num_rows($result) > 0) {                                           /* checks if result is NULL SET*/
            $total_price = 0;
            echo '<table>';
            echo '<tr><th>Ticket ID</th><th>Number of Guests</th><th>Number of Children</th><th>Price</th><th>Ticket Date</th><th>Platform Number</th></tr>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['TicketID'] . '</td>';
                echo '<td>' . $row['NumberOfGuests'] . '</td>';
                echo '<td>' . $row['NumberOfChildren'] . '</td>';                                       /* Each record is displayed */
                echo '<td>' . $row['Price'] . '</td>';
                echo '<td>' . $row['TicketDate'] . '</td>';
                echo '<td>' . $row['PlatformNumber'] . '</td>';
                echo '</tr>';
                $total_price += $row['Price'];                                                          /* Sum up the prices */
            }
            echo '</table>';
            echo '<div style="text-align: center; font-size: 24px; margin-top: 20px;">Total Price: ' . $total_price . '</div>';
        } else {
            echo '<div style="color:red;text-align: center; font-size: 24px; margin-top: 20px;">No results found.</div>';           /* if result is NULL SET  */
        }
    ?>
    <div class="statistics-container">
        <h3>Platform Number with Maximum Profit</h3>
        <p>Platform Number: <?php echo $max_platform['platformnumber']; ?>&nbsp;&nbsp;&nbsp;&nbsp;Total Profit: <?php echo $max_platform['total_profit']; ?></p>
        
        <h3>Month with Maximum Profit</h3>
        <p>Month: <?php echo $max_month['month']; ?>&nbsp;&nbsp;&nbsp;&nbsp;Total Profit: <?php echo $max_month['total_profit']; ?></p>
    </div>
    <button class="button-65" style="position:fixed;top:90%;right:4%;" onclick="location.href='home.html'">Log Out</button>
</body>
</html>
