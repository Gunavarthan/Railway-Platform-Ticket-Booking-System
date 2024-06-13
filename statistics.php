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

    if(isset($_GET['get_json'])){                               //Enters iff the get_json is set true
        $data_set = $_GET['data_set'];                          //option is set 
        $query = "";

        switch ($data_set) {
            case 'TicketCount':
                $query = "SELECT platformnumber, COUNT(*) as value FROM tickets GROUP BY platformnumber ORDER BY platformnumber";
                break;
            case 'TotalRevenue':
                $query = "SELECT platformnumber, SUM(price) as value FROM tickets GROUP BY platformnumber ORDER BY platformnumber";
                break;
            case 'AveragePrice':
                $query = "SELECT platformnumber, AVG(price) as value FROM tickets GROUP BY platformnumber ORDER BY platformnumber";
                break;
            case 'TotalGuests':
                $query = "SELECT platformnumber, SUM(NumberOfGuests) as value FROM tickets GROUP BY platformnumber ORDER BY platformnumber";
                break;
            case 'TotalAdults':
                $query = "SELECT platformnumber, SUM(NumberOfAdults) as value FROM tickets GROUP BY platformnumber ORDER BY platformnumber";
                break;
            case 'TotalChildren':
                $query = "SELECT platformnumber, SUM(NumberOfChildren) as value FROM tickets GROUP BY platformnumber ORDER BY platformnumber";
                break;
            default:
                $query = "SELECT platformnumber, COUNT(*) as value FROM tickets GROUP BY platformnumber ORDER BY platformnumber";
                break;
        }

        $result = mysqli_query($con, $query);
        $data = array();
        
        while($row = mysqli_fetch_assoc($result)){                  //separating each ROW 
            $data[] = $row;
        }

        echo json_encode($data);                                    //sending the output
        exit;                                                       //EXIT is must because PHP also contains HTML properties
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>                       <!-- Import Chart.js Library -->
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

        #dataSelector {
            margin-bottom: 20px;
            padding: 10px;
            font-size: 16px;
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

    <div style="text-align:center;">                                                                    <!-- Drop Down -->
        <label for="dataSelector">Choose data:</label>
        <select id="dataSelector">
            <option value="TicketCount">Ticket Count</option>
            <option value="TotalRevenue">Total Revenue</option>
            <option value="AveragePrice">Average Price</option>
            <option value="TotalGuests">Total Guests</option>
            <option value="TotalAdults">Total Adults</option>
            <option value="TotalChildren">Total Children</option>
        </select>
    </div>

    <canvas id="chart" width="10%" max-width="800px"></canvas>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById("chart").getContext('2d');                              
        let chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],                                                                             //Stores the attributes
                datasets: [{
                    label: 'Ticket Count',
                    data: [],                                                                           //Stores data values
                    backgroundColor: '#007bff',
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true                                                                 //if No value is given the data is set as 0
                    }
                }
            }
        });
    
    function fetchData(option) {
        fetch(`statistics.php?get_json=1&data_set=${option}`)                                              //passing fetch with parameters for separating from HTML objs
        .then(response => response.json())
                .then(data => {
                    const labels = data.map(item => "Platform " + item.platformnumber);                     //creating dictionary from JSON
                    const values = data.map(item => item.value);

                    chart.data.labels = labels;                                                             //Data for Labels
                    chart.data.datasets[0].data = values;                                                   //Data for height
                    chart.data.datasets[0].label = option.replace(/([A-Z])/g, ' $1').trim();                // Format the label {SPACE} 
                    chart.update();
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // Default Option value  
        fetchData('TicketCount');

        // Event listener for the dropdown
        document.getElementById('dataSelector').addEventListener('change', function() {
            fetchData(this.value);
        });
    });
</script>
</html>

