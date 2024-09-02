<?php
$con = mysqli_connect("localhost", "your username", "your password", "train");

$query = "SELECT * FROM Tickets";
if (isset($_POST["search"])) {
    $ticket_id = isset($_POST["ticket_id"]) ? $_POST["ticket_id"] : "";
    $junction = isset($_POST["Junction"]) ? $_POST["Junction"] : ""; 
    $pnr = isset($_POST["pnr"]) ? $_POST["pnr"] : "";
    $from_date = isset($_POST["from_date"]) ? $_POST["from_date"] : "";
    $to_date = isset($_POST["to_date"]) ? $_POST["to_date"] : "";
    $sort_by = isset($_POST["sort_by"]) ? $_POST["sort_by"] : "";

    $query = "SELECT * FROM Tickets WHERE 1=1";

    if (!empty($ticket_id)) {
        $query .= " AND TicketID = '$ticket_id'";
    }
    if (!empty($junction)) {
        $query .= " AND Junction = '$junction'";
    }
    if (!empty($pnr)) {
        $query .= " AND PNR = '$pnr'";
    }
    if (!empty($from_date) && !empty($to_date)) {               // Btwn Dates 
        $query .= " AND TicketDate BETWEEN '$from_date' AND '$to_date'";
    } elseif (!empty($from_date) && empty($to_date)) {          // From date
        $query .= " AND TicketDate >= '$from_date'";
    } elseif (empty($from_date) && !empty($to_date)) {
        $query .= " AND TicketDate <= '$to_date'";              // To date
    }

    if (!empty($sort_by)) {
        $query .= " ORDER BY $sort_by";         // Sorting 
    }

    $result = mysqli_query($con, $query);
} else {
    $result = mysqli_query($con, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="logo.png">
    <title>My Train</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script> <!-- Library for Excel Download  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script> <!-- Save the File -->

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .search-container {
            padding: 20px;
        }

        #searchOptions {
            display: none;
            transition: max-height 0.5s ease-out;
        }

        .show {
            display: block !important;
            max-height: 300px;
        }
        
        .search-container select {
            padding: 10px;
            margin: 5px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
    <script>
        function toggleSearchOptions() {                        
            var x = document.getElementById("searchOptions");               // show the search options
            if (x.classList.contains("show")) {
                x.classList.remove("show");
            } else {
                x.classList.add("show");
            }
        }
    </script>
</head>
<body>
    <div class="navbar">
        <span class="title">My Train</span>
        <a href="search.php">Search</a>
        <a href="#" class="active">Advanced Search</a>
        <a href="statistics.php">Statistics</a>
        <a href="transaction.php" >Transaction</a>
    </div>
    
    <div class="search-container">
        <button onclick="toggleSearchOptions()">More Options</button>
        <form action="asearch.php" method="post">
            <h5>FROM</h5>
            <input type="date" name="from_date" placeholder="From Date"> <!-- input for From Date -->
            <h5>TO</h5>
            <input type="date" name="to_date" placeholder="To Date"> <!-- input for To Date -->
            <div id="searchOptions">
                <input type="text" name="ticket_id" placeholder="Enter Ticket ID"> <!-- input for TicketID -->
                <select id="Junction" name="Junction">
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
                </select>                                           <!-- input for Junction -->
                <input type="text" name="pnr" placeholder="Enter PNR"> <!-- input for PNR -->
                <select name="sort_by"> <!-- input for Sorting -->
                    <option value="">Sort By</option>
                    <option value="TicketID">Ticket ID</option>
                    <option value="TicketDate">Ticket Date</option>
                    <option value="Junction">Junction</option>
                    <option value="PNR">PNR</option>
                </select>
            </div>
            <button type="submit" name="search">Search</button>
        </form>
    </div>

    <?php
    if (isset($_POST["search"])) {
        if (mysqli_num_rows($result) > 0) {
            echo '<table id="myTable">';
            echo '<tr>
                    <th>Ticket ID</th>
                    <th>Number of Guests</th>
                    <th>Number of Adults</th>
                    <th>Number of Children</th>
                    <th>Junction</th>
                    <th>Name 1</th>
                    <th>Name 2</th>
                    <th>Name 3</th>
                    <th>Name 4</th>
                    <th>Name 5</th>
                    <th>Aadhar 1</th>
                    <th>Aadhar 2</th>
                    <th>Aadhar 3</th>
                    <th>Aadhar 4</th>
                    <th>Aadhar 5</th>
                    <th>Price</th>
                    <th>Ticket Date</th>
                    <th>Platform Number</th>
                    <th>PNR</th>
                  </tr>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['TicketID'] . '</td>';
                echo '<td>' . $row['NumberOfGuests'] . '</td>';
                echo '<td>' . $row['NumberOfAdults'] . '</td>';
                echo '<td>' . $row['NumberOfChildren'] . '</td>';
                echo '<td>' . $row['Junction'] . '</td>';
                echo '<td>' . $row['Name1'] . '</td>';
                echo '<td>' . $row['Name2'] . '</td>';
                echo '<td>' . $row['Name3'] . '</td>';
                echo '<td>' . $row['Name4'] . '</td>';
                echo '<td>' . $row['Name5'] . '</td>';
                echo '<td>' . $row['Aadhar1'] . '</td>';
                echo '<td>' . $row['Aadhar2'] . '</td>';
                echo '<td>' . $row['Aadhar3'] . '</td>';
                echo '<td>' . $row['Aadhar4'] . '</td>';
                echo '<td>' . $row['Aadhar5'] . '</td>';
                echo '<td>' . $row['Price'] . '</td>';
                echo '<td>' . $row['TicketDate'] . '</td>';
                echo '<td>' . $row['PlatformNumber'] . '</td>';
                echo '<td>' . $row['PNR'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<div style="color:red;text-align: center; font-size: 24px; margin-top: 20px;">No results found.</div>';
        }
    } else {
        echo '<table id="myTable">';
        echo '<tr>
                <th>Ticket ID</th>
                <th>Number of Guests</th>
                <th>Number of Adults</th>
                <th>Number of Children</th>
                <th>Junction</th>
                <th>Name 1</th>
                <th>Name 2</th>
                <th>Name 3</th>
                <th>Name 4</th>
                <th>Name 5</th>
                <th>Aadhar 1</th>
                <th>Aadhar 2</th>
                <th>Aadhar 3</th>
                <th>Aadhar 4</th>
                <th>Aadhar 5</th>
                <th>Price</th>
                <th>Ticket Date</th>
                <th>Platform Number</th>
                <th>PNR</th>
              </tr>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $row['TicketID'] . '</td>';
            echo '<td>' . $row['NumberOfGuests'] . '</td>';
            echo '<td>' . $row['NumberOfAdults'] . '</td>';
            echo '<td>' . $row['NumberOfChildren'] . '</td>';
            echo '<td>' . $row['Junction'] . '</td>';
            echo '<td>' . $row['Name1'] . '</td>';
            echo '<td>' . $row['Name2'] . '</td>';
            echo '<td>' . $row['Name3'] . '</td>';
            echo '<td>' . $row['Name4'] . '</td>';
            echo '<td>' . $row['Name5'] . '</td>';
            echo '<td>' . $row['Aadhar1'] . '</td>';
            echo '<td>' . $row['Aadhar2'] . '</td>';
            echo '<td>' . $row['Aadhar3'] . '</td>';
            echo '<td>' . $row['Aadhar4'] . '</td>';
            echo '<td>' . $row['Aadhar5'] . '</td>';
            echo '<td>' . $row['Price'] . '</td>';
            echo '<td>' . $row['TicketDate'] . '</td>';
            echo '<td>' . $row['PlatformNumber'] . '</td>';
            echo '<td>' . $row['PNR'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    ?>
    <button class="button-65" style="position:fixed;top:90%;right:4%;" onclick="location.href='home.html'">Log Out</button>
    <button class="button-65" style="position:fixed;top:90%;left:4%;" onclick="DownloadExcel()">Download</button>
    <script>
        function DownloadExcel() { // Convert the HTML table to Excel workbook
            var table = document.getElementById("myTable");
            var workbook = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
            var workbookOut = XLSX.write(workbook, { bookType: 'xlsx', type: 'binary' }); // Write the data into file in Binary format 

            function s2ab(s) { // Convert Binary to ArrayBuffer
                var buf = new ArrayBuffer(s.length);
                var view = new Uint8Array(buf);
                for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
                return buf;
            }

            saveAs(new Blob([s2ab(workbookOut)], { type: "application/octet-stream" }), 'Train.xlsx'); // Saves the excel file
        }
    </script>
</body>
</html>
