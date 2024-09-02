<?php
$con = mysqli_connect("localhost", "your username", "your password", "train");
$searchPerformed = false; // Variable to track if a search was performed

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    $platform_number = $_POST["platform_number"];
    $payment_status = isset($_POST["payment_status"]) ? $_POST["payment_status"] : "";
    $junction = isset($_POST["Junction"]) ? $_POST["Junction"] : ""; // Checks if the junction is provided

    // Constructing the SQL query based on the provided inputs
    $query = "SELECT * FROM tickets WHERE 1=1";
    if (!empty($platform_number)) {
        $query .= " AND PlatformNumber = $platform_number";
    }
    if (!empty($payment_status)) {
        $query .= " AND PayStatus = '$payment_status'";
    }
    if (!empty($junction)) {
        $query .= " AND Junction = '$junction'";
    }

    $result = mysqli_query($con, $query);
    $searchPerformed = true; // Mark that a search has been performed
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo.png">
    <link rel="stylesheet" href="css/style.css">
    <title>Search</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .search-container select {
            padding: 10px;
            margin: 5px;
            width: 150px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .search-container input[type="number"]
        {
            padding: 10px;
            margin: 5px;
            width: 150px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #payment_status option {
            padding: 100px;
        }
        .hover-info {
            display: none;
            position: absolute;
            background-color: rgba(0,123,255,0.8);
            border: 2px solid #f0f0f0;
            border-radius:10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            transition: opacity 1s;
            opacity: 0;
            z-index: 1000;
            height: 200px; 
            color: white; 
            overflow: hidden;
        }
        .hover-info.show {
            display: block;
            opacity: 3;
        }
        .hover-info ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .cancelled {
            background-color: #f8d7da;
            color: #721c24; 
        }
        .cancelled td {
            color: #721c24; 
        }
    </style>
    <script>
        function validateForm() {                                                           // Validates if INPUTS are Given
            var platformNumber = document.getElementById("platform_number").value;
            var status = document.getElementById("payment_status").value;
            var junction = document.getElementById("Junction").value;
            if (platformNumber === "" && status === "" && junction === "") {
                alert("Please enter Valid Input");
                return false;
            }
            return true;
        }

        function showInfo(names, event) {                                                  // Display INFO TAB
            var infoDiv = document.getElementById("hover-info");
            var namesList = infoDiv.querySelector("ul");
            namesList.innerHTML = "";
            names.split(",").forEach(function(name) {
                var li = document.createElement("li");
                li.textContent = name;
                namesList.appendChild(li);
            });

            var rect = event.target.getBoundingClientRect();                               // Position the hover-info div relative to the hovered td
            infoDiv.style.top = (rect.bottom + window.scrollY) + "px";
            infoDiv.style.left = (rect.left + window.scrollX) + "px";

            infoDiv.classList.add("show");
        }

        function hideInfo() {
            var infoDiv = document.getElementById("hover-info");                // HIDE the INFO TAB 
            infoDiv.classList.remove("show");
        }
    </script>
</head>
<body>
    <div class="navbar">
        <span class="title">My Train</span>
        <a href="#" class="active">Search</a>
        <a href="asearch.php">Advanced Search</a>
        <a href="statistics.php">Statistics</a>
        <a href="transaction.php">Transaction</a>
    </div>

    <div class="search-container">
        <form action="search.php" method="post" onsubmit="return validateForm()">
            <input type="number" name="platform_number" id="platform_number" placeholder="Enter Platform Number"> <!-- input for platform number -->
            <select id="payment_status" name="payment_status">
                <option value="" disabled selected style="color:white;">Payment Status</option>
                <option value="Success">Success</option>
                <option value="CANCELLED">Cancelled</option>
            </select>
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
            </select>
            <button type="submit" name="search">Search</button>
        </form>
    </div>

    <div id="hover-info" class="hover-info">
        <u><h4>Names</h4></u>
        <ul></ul>
    </div>

    <?php
    if ($searchPerformed) { // Check if a search was performed
        if (isset($result) && mysqli_num_rows($result) > 0) { /* checks if result is NULL SET */
            echo '<table>';
            echo '<tr><th>Ticket ID</th><th>Number of Guests</th><th>Number of Children</th><th>Price</th><th>Ticket Date</th><th>Platform Number</th><th>Payment Status</th><th>Junction</th></tr>';
            while ($row = mysqli_fetch_assoc($result)) {
                $rowClass = ($row['PayStatus'] === 'CANCELLED') ? 'class="cancelled"' : '';
                $names = implode(",", [$row['Name1'],$row['Aadhar1'],"&nbsp", $row['Name2'],$row['Aadhar2'],"&nbsp", $row['Name3'],$row['Aadhar3'],"&nbsp", $row['Name4'],$row['Aadhar4'],"&nbsp", $row['Name5'],$row['Aadhar5'],"&nbsp"]);  // data for every record
                echo '<tr ' . $rowClass . '>';
                echo '<td onclick="showInfo(\'' . $names . '\', event)" onmouseout="hideInfo()">' . $row['TicketID'] . '</td>';  // visible --> INFO tab {ON - HOVER}
                echo '<td>' . $row['NumberOfGuests'] . '</td>';
                echo '<td>' . $row['NumberOfChildren'] . '</td>'; /* Each record is displayed */
                echo '<td>₹' . $row['Price'] . '</td>';
                echo '<td>' . $row['TicketDate'] . '</td>';
                echo '<td>' . $row['PlatformNumber'] . '</td>';
                echo '<td>' . $row['PayStatus'] . '</td>';
                echo '<td>' . $row['Junction'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<div style="color:red;text-align: center; font-size: 24px; margin-top: 20px;">No results found.</div>'; /* if result is NULL SET */
        }
    }
    ?>
    <button class="button-65" style="position:fixed;top:90%;right:4%;" onclick="location.href='home.html'">Log Out</button>
</body>
</html>
