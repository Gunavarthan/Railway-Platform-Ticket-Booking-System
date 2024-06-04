<?php
$con = mysqli_connect("localhost", "root", "PASSWORD", "train");            //Enter your PASSWORD 

if (isset($_POST["submit"])) {
    // Receiving data from the form
    $numChildren = intval($_POST["numChildren"]); 
    $platformNumber = $_POST["platformNumber"];
    $totalGuests = intval($_POST["GuestNumber"]); // Convert to integer

    // Initialize variables for SQL query
    $query = "";
    $ticketPrice = $numChildren * 20 + ($totalGuests - $numChildren) * 30;

    // Generate a random ticket ID
    $ticketID = mt_rand(100000, 999999);

    // Initialize arrays to store guest names and Aadhar numbers
    $guestNames = [];
    $aadharNumbers = [];

    // Loop through guest inputs and store names and Aadhar numbers
    for ($i = 1; $i <= min(5, $totalGuests); $i++) {
        $guestNames[] = isset($_POST["guestName$i"]) ? $_POST["guestName$i"] : "";              //if input is given or initialized value is added to array
        $aadharNumbers[] = isset($_POST["guestAadhar$i"]) ? $_POST["guestAadhar$i"] : "";       //Else the empty string is stored 
    }

    // Construct the SQL query
    $query = "INSERT INTO Tickets (TicketID, NumberOfGuests, NumberOfAdults, NumberOfChildren, ";

    // Add guest name and Aadhar number fields to the query
    for ($i = 1; $i <= min(5, $totalGuests); $i++) {
        $query .= "Name$i, Aadhar$i, ";
    }

    // Complete the SQL query
    $query .= "Price, PlatformNumber) VALUES ($ticketID, $totalGuests, ($totalGuests - $numChildren), $numChildren, ";

    // Add guest names and Aadhar numbers to the query
    for ($i = 0; $i < min(5, $totalGuests); $i++) {
        $query .= "'{$guestNames[$i]}', '{$aadharNumbers[$i]}', ";
    }

    // Add ticket price and platform number to the query
    $query .= "$ticketPrice, $platformNumber)";

    // Execute the query
    $result = mysqli_query($con, $query);

    // Check if query was successful
    if ($result) {
        echo "Ticket successfully added to the database.";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY Train</title>
    <link rel="stylesheet" href="stylelg.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Guest Information Form</h1>
        <form id="guestForm" method="post">
            <div class="guest-input">
                <label for="guestName1">Guest Name:</label>
                <input type="text" id="guestName1" name="guestName1" placeholder="Enter guest name" required>
                <label for="guestAadhar1">Aadhar Number:</label>
                <input type="text" id="guestAadhar1" name="guestAadhar1" placeholder="Enter Aadhar number" required>
                <button type="button" onclick="addGuest()">Add Guest</button>
                <button type="button" onclick="removeGuest(this)">Remove Guest</button>
                <hr>
            </div>
            <div class="guest-input">
                <label for="numChildren">Number of Children:</label>
                <input type="integer" id="numChildren" name="numChildren" min="0" style="width: calc(50% - 20px);">
            </div>
            <div class="guest-input">
                <label for="GuestNumber">Number of guests:</label>
                <input type="integer" id="GuestNumber" name="GuestNumber" min="0" style="width: calc(50% - 20px);" default="0">
            </div>
            <div class="guest-input">
                <label for="platformNumber">Platform Number:</label>
                <input type="text" id="platformNumber" name="platformNumber" placeholder="Enter platform number" style="width: calc(50% - 20px);" required>
            </div>
            <button type="submit" class="submit-button" name="submit">Submit</button>
        </form>
    </div>

    <script>
        let guestCount = 1;                         //initializing guest count

        function addGuest() {
            if (guestCount < 5) {
                guestCount++;
                const guestDiv = document.createElement("div");        //creating DOM element div and storing in constant guestDiv
                guestDiv.className = "guest-input";
                guestDiv.innerHTML = `
                    <label for="guestName${guestCount}">Guest Name:</label>
                    <input type="text" id="guestName${guestCount}" name="guestName${guestCount}" placeholder="Enter guest name" required>
                    <label for="guestAadhar${guestCount}">Aadhar Number:</label>
                    <input type="text" id="guestAadhar${guestCount}" name="guestAadhar${guestCount}" placeholder="Enter Aadhar number" required>
                    <button type="button" onclick="addGuest()" ${guestCount === 5 ? 'disabled' : ''}>Add Guest</button>             
                    <button type="button" onclick="removeGuest(this)">Remove Guest</
                    <hr>
                `;                //{guestCount === 5 ? 'disabled' : ''} -> Checks if the guest count is greater than 5 button is disabled 
                document.getElementById("guestForm").insertBefore(guestDiv, document.getElementById("numChildren").parentElement);      //inserting the DOM element guestDiv before numChildren
            }
        }

        function removeGuest(button) {
            const guestDiv = button.parentElement;          //initializing the guestDiv as parent of button 
            if (guestCount > 1) {                           // if Guest input is more then 1 
                guestDiv.remove();                          //then allow to remove the guestDiv element from the DOM
                guestCount--;
            }
        }
    </script>
</body>
</html>
