<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My train</title>
    <link rel="stylesheet" href="css/stylelg.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="log-container">
        <h2>Enter Your PNR / Ticket Number</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="PNR">Ticket Number:</label>
                <input type="text" id="PNR" name="PNR" maxlength="10" required>                <!--Getting inputs for the PNR-->
            </div>
            <div><b>***Platform Tickets are provided only 2 hours before the Train Arrival time</b></div>
            <div class="form-group">
                <button type="submit">Submit</button>
            </div>
        </form>
        <?php
            date_default_timezone_set('Asia/Kolkata');                         //defining the TimeZone
            $con = mysqli_connect("localhost", "your username", "your password", "train");
            if (isset($_POST["PNR"])) {
                $PNR = mysqli_real_escape_string($con, $_POST["PNR"]);
                $query = "delete from tickets where TicketID = 'UnInit'";       //Checks for non initialized records and delete 
                mysqli_query($con, $query);
                $query = "SELECT * FROM pnr WHERE PNR='$PNR'";                   //selecting rows with same PNR value
                $result = mysqli_query($con, $query);

                if (mysqli_num_rows($result) > 0) {                                                 //if Result rows is greater than 0  
                    $row = mysqli_fetch_assoc($result);

                    $Today = date('Y-m-d');                         //Current date
                    $CurrTime = strtotime(date('H:i:s'));           //Current Time
                    $travelTime = strtotime($row['TravelTime']);    //time of travel
                    $validTime = $travelTime - 2 * 3600;            //Two hours before travel
                    $validEndTime = $travelTime + 1 * 3600;         // One hour after travel

                    if($row['TravelDate'] == $Today && $CurrTime >= $validTime && $CurrTime <= $validEndTime){                 //PNR with Date as same and time just 2hrs before or 1hr after the travel time are Redirect to Next Page 
                        $query = "insert into tickets (TicketID,pnr,NumberOfGuests,NumberOfAdults,NumberOfChildren,Price,PlatformNumber) values ('UnInit',$PNR,-0101,-0101,-0101,-0101,-0101)";
                        //echo("$CurrTime >= $validTime   $CurrTime <= $validEndTime");
                        mysqli_query($con, $query);
                        echo "<script>window.location.href='ticketinput.php';</script>";         
                    } 
                    else{
                        //echo("$CurrTime >= $validTime   $CurrTime <= $validEndTime");
                        echo "<div class='alert'>PNR schedule mismatch </div>";    
                    }
                } else {
                    echo "<div class='alert'>PNR not found</div>";              //Else return a Error Message
                }
            }

            mysqli_close($con);
        ?>
    </div>
</body>
</html>
