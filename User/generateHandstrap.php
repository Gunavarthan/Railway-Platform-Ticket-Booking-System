<!DOCTYPE html>
<html>
<head>
    <title>Hand Straps - Indian Railways</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .print-area {
            display: flex;
            flex-wrap: wrap;
            gap: 10px; /* Adjust the gap between columns */
            justify-content: center;
        }
        .hand-strap {
            width: 2.5cm;
            height: 20cm;
            border: 1px solid #000;
            margin: 10px auto;
            text-align: center;
            padding: 5px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }
        .header {
            background-color: #007bff;
            color: white;
            width: 100%;
            padding: 5px 0;
            font-size: 12px; /* Adjust header font size */
            font-weight: bold;
        }
        .hand-strap img {
            margin-top: 5px;
            max-width: 100%;
        }
        .details {
            font-size: 12px; /* Adjust details font size */
            margin: 5px 0; /* Adjust margin */
            text-align: left;
            width: 100%;
        }
        .details span {
            font-weight: bold;
            display: block;
            margin: 2px 0;
        }
        .no-print {
            display: block;
            margin: 20px auto;
            text-align: center;
        }
        .column {
            width: 20%; /* Adjust column width as needed */
        }
        .continue-button {
    margin-top: 20px;
    padding: 10px 20px;
    font-size: 16px;
}


.button-65 {
    z-index: 10;
  appearance: none;
  backface-visibility: hidden;
  background-color: #2f80ed;
  border-radius: 10px;
  border-style: none;
  box-shadow: none;
  box-sizing: border-box;
  color: #fff;
  cursor: pointer;
  display: inline-block;
  font-family: Inter,-apple-system,system-ui,"Segoe UI",Helvetica,Arial,sans-serif;
  font-size: 15px;
  font-weight: 500;
  height: 50px;
  letter-spacing: normal;
  line-height: 1.5;
  outline: none;
  overflow: hidden;
  padding: 14px 30px;
  position: relative;
  text-align: center;
  text-decoration: none;
  transform: translate3d(0, 0, 0);
  transition: all .3s;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  vertical-align: top;
  white-space: nowrap;
}

.button-65:hover {
  background-color: #1366d6;
  box-shadow: rgba(0, 0, 0, .05) 0 5px 30px, rgba(0, 0, 0, .05) 0 1px 4px;
  opacity: 1;
  transform: translateY(0);
  transition-duration: .35s;
}

.button-65:hover:after {
  opacity: .5;
}

.button-65:active {
  box-shadow: rgba(0, 0, 0, .1) 0 3px 6px 0, rgba(0, 0, 0, .1) 0 0 10px 0, rgba(0, 0, 0, .1) 0 1px 4px -1px;
  transform: translateY(2px);
  transition-duration: .35s;
}

.button-65:active:after {
  opacity: 1;
}

@media (min-width: 768px) {
  .button-65 {
    padding: 14px 22px;
    width: 176px;
  }}
    </style>
</head>
<body>
<div class="print-area">
    <?php
    // Include phpqrcode library
    include 'phpqrcode/qrlib.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guests'])) {
        $selectedGuests = $_POST['guests'];

        // Retrieve the ticket data from the hidden input field
        $ticketData = json_decode($_POST['ticketData'], true);
        $ticketID = htmlspecialchars($ticketData['TicketID']);
        $ticketDate = htmlspecialchars($ticketData['TicketDate']);
        $ticketTime = htmlspecialchars($ticketData['TicketTime']);
        $secNum = htmlspecialchars($ticketData['SecNum']);

        // Calculate number of columns needed based on number of tickets
        $numColumns = count($selectedGuests) > 0 ? ceil(count($selectedGuests) / 5) : 1;

        // Split guests into columns
        $columns = array_chunk($selectedGuests, $numColumns);

        // Directory to store QR codes (make sure it's writable)
        $qrCodePath = 'qrcodes/';

        // Loop through columns and display tickets
        foreach ($columns as $column) {
            echo "<div class='column'>";
            foreach ($column as $guest) {
                list($name, $phone, $junction, $platformNumber) = explode('|', $guest);
                $qrData = "Ticket ID: " . $ticketID . "\n"
                        . "Name: " . htmlspecialchars($name) . "\n"
                        . "Primary Phone No: " . htmlspecialchars($phone) . "\n"
                        . "Secondary Phone No: " . htmlspecialchars($secNum) . "\n"
                        . "Junction: " . htmlspecialchars($junction) . "\n"
                        . "Platform No: " . htmlspecialchars($platformNumber) . "\n"
                        . "Ticket Date: " . $ticketDate . "\n"
                        . "Ticket Time: " . $ticketTime;

                // Filename for the QR code
                $qrCodeFile = $qrCodePath . $ticketID . '_' . urlencode($name) . '.png';

                // Generate QR code
                QRcode::png($qrData, $qrCodeFile, QR_ECLEVEL_L, 5);

                // HTML output for each ticket
                echo "<div class='hand-strap'>";
                echo "<div class='header'>Indian Railways</div><br>";
                echo "<div class='header'>Platform Ticket</div><br>";
                echo "<img src='" . $qrCodeFile . "' alt='QR Code'><hr>";
                echo "<div class='details'>";
                echo "<span>Ticket ID:</span> <b>" . $ticketID . "</b><br><hr><br>";
                echo "<span>Name:</span> <b>" . htmlspecialchars($name) . "</b><br><hr><br>";
                echo "<span>Primary Phone No:</span> <b>" . htmlspecialchars($phone) . "</b><br><hr><br>";
                echo "<span>Secondary Phone No:</span> <b>" . htmlspecialchars($secNum) . "</b><br><hr><br>";
                echo "<span>Junction:</span> <b>" . htmlspecialchars($junction) . "</b><br><hr><br>";
                echo "<span>Platform No: <b>" . htmlspecialchars($platformNumber) . "</b><br><hr><br></span>";
                echo "<span>Ticket Date:</span> <b>" . $ticketDate . "</b><br><hr><br>";
                echo "<span>Ticket Time:</span> <b>" . $ticketTime . "</b>";
                echo "</div>";
                echo "</div>";
            }
            echo "</div>";
        }
    } else {
        echo "<p>No guest names selected.</p>";
    }
    ?>
</div>
<div >
<center><button class='button-65' onclick="window.print()" class="btn btn-primary no-print">Print Hand Straps</button>
<button class='button-65' style='position:fixed;top:90%;right:4%;' onclick='home()'>Log Out</button></center></div>
<script>
    function home(){
        window.location = 'home.html';
    }
</script>
</body>
</html>
