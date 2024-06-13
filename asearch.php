<?php
    $con = mysqli_connect("localhost", "root", "guna", "train");

    if (isset($_POST["search"])) {
        $ticket_id = $_POST["ticket_id"];
        $ticket_date = $_POST["ticket_date"];

        if (!empty($ticket_id) && !empty($ticket_date)) {
            $query = "SELECT * FROM Tickets WHERE TicketID = $ticket_id AND TicketDate >= '$ticket_date'";   //If both ticketID and the ticketDate is given 
        } elseif (!empty($ticket_id)) {
            $query = "SELECT * FROM Tickets WHERE TicketID = $ticket_id";                                   //If only ticketID is given
        } elseif (!empty($ticket_date)) {
            $query = "SELECT * FROM Tickets WHERE TicketDate >= '$ticket_date'";                             //If only ticketDate is given
        } else {
            $query = "SELECT * FROM Tickets";                                                               //If no input is given
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>         <!-- Library for Excel Download  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>  <!-- Save the File -->

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <span class="title">My Train</span>
        <a href="search.php">Search</a>
        <a href="#" class="active">Advanced Search</a>
        <a href="statistics.php">Statistics</a>
    </div>
    
    <div class="search-container">
        <form action="asearch.php" method="post">
            <input type="text" name="ticket_id" placeholder="Enter Ticket ID">                                  <!--input for TicketID-->
            <input type="date" name="ticket_date" placeholder="YYYY-MM-DD">                                     <!--input for TicketDate-->
            <button type="submit" name="search">Search</button>
        </form>
    </div>

    <?php
        if (isset($result) && mysqli_num_rows($result) > 0) {
            echo '<table id="myTable">';
            echo '<tr>
                    <th>Ticket ID</th>
                    <th>Number of Guests</th>
                    <th>Number of Adults</th>
                    <th>Number of Children</th>
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
                  </tr>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['TicketID'] . '</td>';
                echo '<td>' . $row['NumberOfGuests'] . '</td>';
                echo '<td>' . $row['NumberOfAdults'] . '</td>';
                echo '<td>' . $row['NumberOfChildren'] . '</td>';
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
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<div style="color:red;text-align: center; font-size: 24px; margin-top: 20px;">No results found.</div>';
        }
    ?>
    <button class="button-65" style="position:fixed;top:90%;right:4%;" onclick="location.href='home.html'">Log Out</button>
    <button class="button-65" style="position:fixed;top:90%;left:4%;" onclick="DownloadExcel()">Download</button>
    <script>
        function DownloadExcel(){                                                             //Convert the HTML table to Excel workbook
            var table = document.getElementById("myTable");
            var workbook = XLSX.utils.table_to_book(table,{sheet:"Sheet1"});
            var workbookOut = XLSX.write(workbook,{bookType:'xlsx',type:'binary'});            //Write the data into file in Binary format 

            function s2ab(s){                                                                  //Convert Binary to ArrayBuffer
                var buf = new ArrayBuffer(s.length);
                var view = new Uint8Array(buf);
                for(var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
                return buf;
            }

            saveAs(new Blob([s2ab(workbookOut)],{type:"application/octet-stream"}),'Train.xlsx');   //Saves the excel file
        }
    </script>
</body>
</html>
