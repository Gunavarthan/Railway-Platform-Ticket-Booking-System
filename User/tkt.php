<?php
// Include the PHP QR Code library
include('phpqrcode/qrlib.php');

// Database connection
$con = mysqli_connect("localhost", "your username", "your password", "train");

// Check connection
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL query to fetch the last updated record from the Tickets table
$sql = "SELECT *, TIME(TicketTime) AS TicketTimeFormatted FROM Tickets ORDER BY TicketDate DESC, TicketTime DESC LIMIT 1"; // Retrieve the TicketTime column and alias it as TicketTimeFormatted
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // Format ticket data into a simple string
    $parsedData = "Ticket ID: " . $row["TicketID"] . "\n"
                . "Ticket Date: " . $row["TicketDate"] . "\n"
                . "Ticket Time: " . $row["TicketTimeFormatted"] . "\n"
                . "End Time: " . date('H:i:s', strtotime($row["TicketTimeFormatted"]) + 7200) . "\n"
                . "Junction: " . $row["Junction"] . "\n"
                . "Platform Number: " . $row["PlatformNumber"] . "\n"
                . "PNR: " . $row["PNR"] . "\n"
                . "No. Of Guests: " . $row["NumberOfGuests"] . "\n"
                . "No. Of Adults: " . $row["NumberOfAdults"] . "\n"
                . "No. Of Children: " . $row["NumberOfChildren"] . "\n"
                . "Name: " . $row["Name1"] . "\n"
                . "Phone Number: " . $row["Phone1"] . "\n"
                . "Name: " . $row["Name2"] . "\n"
                . "Phone Number: " . $row["Phone2"] . "\n"
                . "Name: " . $row["Name3"] . "\n"
                . "Phone Number: " . $row["Phone3"] . "\n"
                . "Name: " . $row["Name4"] . "\n"
                . "Phone Number: " . $row["Phone4"] . "\n"
                . "Name: " . $row["Name5"] . "\n"
                . "Phone Number: " . $row["Phone5"] . "\n"
                . "Secondary Phone Number: " . $row["SecNum"] . "\n"
                . "Price: " . $row["Price"];

    // Generate QR code and save it as an image
    $qrFilePath = 'qrcodes/ticket_qr.png';
    QRcode::png($parsedData, $qrFilePath, QR_ECLEVEL_L, 4);

    // Start HTML output (Your original structure)
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Indian Railways</title>
        
        <!-- Bootstrap CSS -->
        <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet'>
        <link rel='stylesheet' href='css/style.css'>
        <style>
            .container {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                flex-direction: column;
            }
            .card {
                position: relative;
                width: 100%;
                max-width: 600px;
                border: 1px solid #ccc;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
            }
            .header {
                display: flex;
                justify-content: center;
                align-items: center;
                background-color: #007bff;
                color: white;
                font-size: 32px;
                text-align: center;
                padding: 10px 0;
            }
            .header img {
                height: 80px;
                margin-right: 20px;
            }
            .data {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-top: 20px;
            }
            .ticket-details {
                width: calc(50% - 10px);
                text-align: left;
            }
            .details {
                width: calc(50% - 10px);
                text-align: center;
            }
            .ticket-details div,
            .details div {
                padding: 10px 0;
            }
            .ticket-details div span {
                font-weight: bold;
                display: inline-block;
                width: 150px;
            }
            .details img {
                margin-top: 10px;
                max-width: 200px;
                width: 100%;
            }
            .price {
                font-size: 24px;
                font-weight: bold;
                margin-top: 10px;
            }
            .btn-container {
                display: flex;
                justify-content: space-between;
                width: 100%;
                max-width: 600px;
            }
            .btn-container button,
            .btn-container a {
                width: calc(50% - 10px);
            }
            .valid-message {
                text-align: center;
                margin-top: 20px;
            }
            @media print {
                body * {
                    visibility: hidden;
                }
                .card, .card * {
                    visibility: visible;
                }
                .card {
                    position: absolute;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                }
                .no-print {
                    display: none;
                }
            }
        </style>
        
    </head>
    <body><center><br><br>
    
        <div class='card'>
            <div class='header'>
                <img src='logo.png' alt='Logo'> &nbsp&nbsp&nbsp
                <div>
                    Indian Railways
                    <br>
                    Platform Ticket
                </div>
            </div>";

    echo "<div class='data'>";
    echo "<div class='ticket-details'>";
    echo "<div><span>Ticket ID</span><b>:</b> <b>" . $row["TicketID"] . "</b></div>";
    echo "<div><span>Ticket Date</span><b>:</b> <b>" . $row["TicketDate"] . "</b></div>";
    echo "<div><span>Ticket Time</span><b>:</b> <b>" . $row["TicketTimeFormatted"] . "</b></div>";
    echo "<div><span>End Time</span><b>:</b> <b>" . date('H:i:s', strtotime($row["TicketTimeFormatted"]) + 7200) . "</b></div>";
    echo "<div><span>Junction</span><b>:</b> <b>" . $row["Junction"] . "</b></div>";
    echo "<div><span>Platform No</span><b>:</b> <b>" . $row["PlatformNumber"] . "</b></div>"; 
    echo "<div><span>PNR</span><b>:</b> <b>" . $row["PNR"] . "</b></div>";
    echo "<div><span>No. Of Guests</span><b>:</b> <b>" . $row["NumberOfGuests"] . "</b></div>";
    echo "<div><span>No. Of Adults</span><b>:</b> <b>" . $row["NumberOfAdults"] . "</b></div>";
    echo "<div><span>No. Of Children</span><b>:</b> <b>" . $row["NumberOfChildren"] . "</b></div>";
    echo "</div>";
    echo "<div class='details'><br><br><br>";
    echo "<div><img src='" . $qrFilePath . "' alt='QR Code'></div>";
    echo "<div class='price'>Price: <b>" . $row["Price"] . "</b></div>";
    echo "</div>";
    echo "</div>";
    echo "<div class='valid-message'><b><h1>Valid For 2 Hours Only!!</h1></b></div>";
    echo "</div><br>
        <div class='btn-container'>
            <button onclick='printTable()' class='btn btn-primary no-print'>Print Ticket</button>
            <a href='Handstrap.php' class='btn btn-secondary' role='button'>Print Hand straps</a>
        </div>
<br><br>
            <button class='button-65' style='position:fixed;top:90%;right:4%;' onclick='home()'>Log Out</button>

    <!-- Bootstrap JS and dependencies -->
    <script src='https://code.jquery.com/jquery-3.5.1.slim.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>

    <script>
    function home(){
        window.location = 'home.html';
    }
    function printTable() {
        window.print();
    }
    </script>
</center>
    </body>
    </html>";

    mysqli_close($conn);
} else {
    // Handle case where no results are found
    echo "No ticket details found.";
}

?>
