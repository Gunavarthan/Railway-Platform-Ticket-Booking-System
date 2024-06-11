<?php
    $con = mysqli_connect("localhost", "root", "guna", "train");
    if (isset($_POST["search"])) {
        $platform_number = $_POST["platform_number"];
        $query = "SELECT * FROM tickets WHERE PlatformNumber = $platform_number";               /* records with platform Number as input is retrieved  */
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
    </style>
    <script>
         function validateForm() {
            var platformNumber = document.getElementById("platform_number").value;
            if (platformNumber === "") {
                alert("Please enter a platform number");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="navbar">
        <span class="title">My Train</span>
        <a href="#" class="active">Search</a>
        <a href="asearch.php">Advanced Search</a>
        <a href="statistics.php">Statistics</a>
    </div>
    

    <div class="search-container">
        <form action="search.php" method="post" onsubmit="return validateForm()">
            <input type="text" name="platform_number" id="platform_number" placeholder="Enter Platform Number">              <!--input for platform number-->          
            <button type="submit" name="search">Search</button>                                         
        </form>
    </div>

    
    <?php
        if (isset($result) && mysqli_num_rows($result) > 0) {                                           /* checks in result is NULL SET*/
            echo '<table>';
            echo '<tr><th>Ticket ID</th><th>Number of Guests</th><th>Number of children</th><th>Price</th><th>Ticket Date</th><th>Platform Number</th></tr>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['TicketID'] . '</td>';
                echo '<td>' . $row['NumberOfGuests'] . '</td>';
                echo '<td>' . $row['NumberOfChildren'] . '</td>';                                       /* Each record is displayed */
                echo '<td>' . $row['Price'] . '</td>';
                echo '<td>' . $row['TicketDate'] . '</td>';
                echo '<td>' . $row['PlatformNumber'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<div style="color:red;text-align: center; font-size: 24px; margin-top: 20px;">No results found.</div>';           /* if result is NULL SET  */
        }
    ?>
    <button class="button-65" style="position:fixed;top:90%;right:4%;" onclick="location.href='home.html'">Log Out</button>
</body>
</html>
