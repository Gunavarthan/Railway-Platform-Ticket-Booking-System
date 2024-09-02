<?php
$con = mysqli_connect("localhost", "your username", "your password", "train");

// Query to get platform number with maximum profit
$max_platform_query = "
    SELECT platformnumber, SUM(price) as total_profit            
    FROM tickets
    GROUP BY platformnumber
    ORDER BY total_profit DESC
    LIMIT 1
";
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
        $query .= " WHERE " . implode(' AND ', $conditions);
    }

    $result = mysqli_query($con, $query);
}

if(isset($_GET['get_json'])){
    $data_set = $_GET['data_set'];
    $junction = $_GET['junction'];
    $query = "";

    switch ($data_set) {
        case 'TicketCount':
            $query = "SELECT platformnumber, COUNT(*) as value FROM tickets";
            break;
        case 'TotalRevenue':
            $query = "SELECT platformnumber, SUM(price) as value FROM tickets";
            break;
        case 'AveragePrice':
            $query = "SELECT platformnumber, AVG(price) as value FROM tickets";
            break;
        case 'TotalGuests':
            $query = "SELECT platformnumber, SUM(NumberOfGuests) as value FROM tickets";
            break;
        case 'TotalAdults':
            $query = "SELECT platformnumber, SUM(NumberOfAdults) as value FROM tickets";
            break;
        case 'TotalChildren':
            $query = "SELECT platformnumber, SUM(NumberOfChildren) as value FROM tickets";
            break;
        default:
            $query = "SELECT platformnumber, COUNT(*) as value FROM tickets";
            break;
    }

    if (!empty($junction)) {
        $query .= " WHERE junction = '$junction'";
    }

    $query .= " GROUP BY platformnumber ORDER BY platformnumber";

    $result = mysqli_query($con, $query);
    $data = array();
    
    while($row = mysqli_fetch_assoc($result)){
        $data[] = $row;
    }

    echo json_encode($data);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo.png">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        #dataSelector, #junctionSelector {
            margin-bottom: 20px;
            padding: 10px;
            font-size: 16px;
        }
        .chart-container {
            width: 80%; 
            margin: 0 auto;
        }
        #chart {
            max-height: 800px;
            width: 100%; 
        }
        @media (max-width: 1200px) {
            .chart-container {
                width: 90%;
            }
        }
        @media (max-width: 800px) {
            .chart-container {
                width: 100%;
            }
            #chart {
                max-height: 600px; 
            }
        }
        @media (max-width: 600px) {
            #chart {
                max-height: 400px; 
            }
        }
        .hidden {
            display: none;
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
    </style>
</head>
<body>
    <div class="navbar">
        <span class="title">My Train</span>
        <a href="search.php">Search</a>
        <a href="asearch.php">Advanced Search</a>
        <a href="#" class="active">Statistics</a>
        <a href="transaction.php" >Transaction</a>
    </div>

    <div class="search-container">
        <form action="statistics.php" method="post">
            <input type="date" name="ticket_date" placeholder="Enter Ticket Date">
            <input type="text" name="platform_number" placeholder="Enter Platform Number">
            <button type="submit" name="search">Search</button>
        </form>
    </div>
    
    <?php
        if (isset($result) && mysqli_num_rows($result) > 0) {
            $total_price = 0;
            $total_guests = 0;
            $total_children = 0;
            echo '<table>';
            echo '<tr><th>Ticket ID</th><th>Number of Guests</th><th>Number of Children</th><th>Price</th><th>Ticket Date</th><th>Platform Number</th></tr>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['TicketID'] . '</td>';
                echo '<td>' . $row['NumberOfGuests'] . '</td>';
                echo '<td>' . $row['NumberOfChildren'] . '</td>';
                echo '<td>₹' . $row['Price'] . '</td>';
                echo '<td>' . $row['TicketDate'] . '</td>';
                echo '<td>' . $row['PlatformNumber'] . '</td>';
                echo '</tr>';
                $total_price += $row['Price'];
                $total_guests += $row['NumberOfGuests'];
                $total_children += $row['NumberOfChildren'];
            }
            echo '</table>';
            echo '<div class="total-info" style="text-align: center; font-size: 24px; margin-top: 20px;">';
            echo 'Total Price: ₹' . $total_price . ' &nbsp; &nbsp; &nbsp; ';
            echo 'Total Guests: ' . $total_guests . ' &nbsp; &nbsp; &nbsp; ';
            echo 'Total Children: ' . $total_children;
            echo '</div>';
        } else {
            echo '<div style="color:red;text-align: center; font-size: 24px; margin-top: 20px;">No results found.</div>';
        }
    ?>

    <div class="statistics-container">
        <h3>Platform Number with Maximum Collection</h3>
        <p>Platform Number: <?php echo $max_platform['platformnumber']; ?>&nbsp;&nbsp;&nbsp;&nbsp;Total Collection: ₹<?php echo $max_platform['total_profit']; ?></p>
        
        <h3>Month with Maximum Collection</h3>
        <p>Month: <?php echo $max_month['month']; ?>&nbsp;&nbsp;&nbsp;&nbsp;Total Collection: ₹<?php echo $max_month['total_profit']; ?></p>
    </div>

    <button class="button-65" id="logoutButton" style="position:fixed;top:90%;right:4%;" onclick="location.href='home.html'">Log Out</button>
    <button id="showGraphButton" class="button-65" style="position:fixed;top:90%;left:4%;">Show Graph</button>

    <div class="chart-container">
        <canvas id="chart"></canvas>
    </div>

    <div style="text-align:center;">
        <label for="dataSelector">Choose data:</label>
        <select id="dataSelector">
            <option value="TicketCount">Ticket Count</option>
            <option value="TotalRevenue">Total Revenue</option>
            <option value="AveragePrice">Average Price</option>
            <option value="TotalGuests">Total Guests</option>
            <option value="TotalAdults">Total Adults</option>
            <option value="TotalChildren">Total Children</option>
        </select>

        <label for="junctionSelector">Choose junction:</label>
        <select id="junctionSelector">
            <option value="">Select</option>
            <option value="SALEM">SALEM</option>
            <option value="KARUR">KARUR</option>
            <option value="DINDIGUL">DINDIGUL</option>
            <option value="MADURAI">MADURAI</option>
            <option value="VIRUDHUNAGAR">VIRUDHUNAGAR</option>
            <option value="COIMBATORE">COIMBATORE</option>
            <option value="CHENGAIPATTU">CHENGAIPATTU</option>
            <option value="TIRUPPUR">TIRUPPUR</option>
            <option value="ERODE">ERODE</option>
            <option value="THENI">THENI</option>
        </select>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartCanvas = document.getElementById('chart');
            const ctx = chartCanvas.getContext('2d');
            let chart = null;

            function toggleGraph() {
                const chartDiv = document.getElementById('chart');
                chartDiv.style.display = (chartDiv.style.display === 'none') ? 'block' : 'none';
                if (chart === null && chartDiv.style.display === 'block') {
                    initChart(); // Initialize chart if it's visible and not already initialized
                }
                const button = document.getElementById('showGraphButton');
                if (chartDiv.style.display === 'block') {
                    button.innerHTML = "Hide Graph";
                } else {
                    button.innerHTML = "Show Graph";
                }
                // Scroll to the bottom of the page
                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
            }

            function initChart() {
                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Ticket Count',
                            data: [],
                            backgroundColor: '#007bff',
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Fetch initial data when chart is initialized
                fetchData('TicketCount', '');
            }

            function fetchData(option, junction) {
                fetch(`statistics.php?get_json=1&data_set=${option}&junction=${junction}`)
                .then(response => response.json())
                .then(data => {
                    const labels = data.map(item => "Platform " + item.platformnumber);
                    const values = data.map(item => item.value);

                    chart.data.labels = labels;
                    chart.data.datasets[0].data = values;
                    chart.data.datasets[0].label = option.replace(/([A-Z])/g, ' $1').trim();
                    chart.update();
                })
                .catch(error => console.error('Error fetching data:', error));
            }

            // Event listener for the "Show Graph" button
            document.getElementById('showGraphButton').addEventListener('click', toggleGraph);

            // Event listener for the dropdowns
            document.getElementById('dataSelector').addEventListener('change', function() {
                const junction = document.getElementById('junctionSelector').value;
                fetchData(this.value, junction);
            });

            document.getElementById('junctionSelector').addEventListener('change', function() {
                const dataOption = document.getElementById('dataSelector').value;
                fetchData(dataOption, this.value);
            });

            // Hide or show buttons based on scroll position
            window.addEventListener('scroll', function() {
                const logoutButton = document.getElementById('logoutButton');
                const showGraphButton = document.getElementById('showGraphButton');
                const scrollPosition = window.scrollY + window.innerHeight;
                const pageHeight = document.documentElement.scrollHeight;

                if (scrollPosition >= pageHeight) {
                    logoutButton.classList.add('hidden');
                    showGraphButton.classList.add('hidden');
                } else {
                    logoutButton.classList.remove('hidden');
                    showGraphButton.classList.remove('hidden');
                }
            });
        });
    </script>
</body>
</html>
