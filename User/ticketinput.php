<?php
// Database connection
$con = mysqli_connect("localhost", "your username", "your password", "train");
$aadhar_con = mysqli_connect("localhost", "your username", "your password", "train");

if (!$con || !$aadhar_con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to generate custom ticket ID
function generateCustomTicketID($destination, $length = 8) {
    if ($length < 6) {
        throw new Exception("Length must be at least 6 to accommodate 3 letters and 4 numbers.");
    }

    $destinationLetters = strtoupper(substr($destination, 0, 3));
    $numbers = '0123456789';
    $numberLength = strlen($numbers);
    $randomNumbers = '';
    for ($i = 0; $i < ($length - 3); $i++) {
        $randomNumbers .= $numbers[rand(0, $numberLength - 1)];
    }

    return $destinationLetters . $randomNumbers;
}

// Form handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numChildren = intval($_POST["numChildren"]);
    $secNum = substr(mysqli_real_escape_string($con, $_POST["secNum"]), 0, 15); // Truncate to 15 characters
    $platformNumber = intval($_POST["platformNumber"]);
    $totalGuests = intval($_POST["guestCount"]);
    $destination = mysqli_real_escape_string($con, $_POST["destination"]);
    
    if ($platformNumber < 1) {
        echo "<script>alert('Error: Platform number must be at least 1.'); window.history.back();</script>";
        exit;
    }

    $childPrice = 5;
    $adultPrice = 10;
    $ticketPrice = $numChildren * $childPrice + ($totalGuests - $numChildren) * $adultPrice;

    try {
        $ticketID = generateCustomTicketID($destination);
    } catch (Exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.history.back();</script>";
        exit;
    }

    $guestNames = [];
    $aadharNumbers = [];
    $phoneNumbers = [];
    $applyToAll = isset($_POST["applyToAll"]) && $_POST["applyToAll"] == "on";
    $valid = true;

    for ($i = 1; $i <= min(5, $totalGuests); $i++) {
        if (!empty($_POST["guestName$i"]) && !empty($_POST["guestAadhar$i"])) {
            $guestName = mysqli_real_escape_string($con, $_POST["guestName$i"]);
            $guestAadhar = mysqli_real_escape_string($con, $_POST["guestAadhar$i"]);

            // Check Aadhar details
            $aadharCheckQuery = "SELECT `Name`, `Aadhar_No` FROM guests WHERE `Name` = '$guestName' AND `Aadhar_No` = '$guestAadhar'";
            $aadharCheckResult = mysqli_query($aadhar_con, $aadharCheckQuery);

            if (mysqli_num_rows($aadharCheckResult) == 0) {
                $valid = false;
                echo "<script>alert('Error: Name and Aadhar number do not match for Guest $i.'); window.history.back();</script>";
                exit;
            } else {
                // Alert that name and Aadhar number are correct
                //echo "<script>alert('Name and Aadhar number are correct for Guest $i.');</script>";
            }

            $guestNames[] = $guestName;
            $aadharNumbers[] = $guestAadhar;

            if ($applyToAll && !empty($_POST["guestPhone1"])) {
                $phoneNumbers[] = mysqli_real_escape_string($con, $_POST["guestPhone1"]);
            } else {
                $phoneNumbers[] = mysqli_real_escape_string($con, $_POST["guestPhone$i"]);
            }
        }
    }

    if ($valid) {
        // Fetch PNR value and delete any previous record with TicketID 'UnInit'
        $query = "SELECT PNR FROM tickets WHERE TicketID = 'UnInit'";
        $PNRResult = mysqli_query($con, $query);

        if (!$PNRResult) {
            die("Error fetching PNR: " . mysqli_error($con));
        }

        $PNRRow = mysqli_fetch_assoc($PNRResult);
        $PNR = $PNRRow['PNR'];

        // Delete previous record
        $query = "DELETE FROM tickets WHERE TicketID = 'UnInit'";
        if (!mysqli_query($con, $query)) {
            die("Error deleting record: " . mysqli_error($con));
        }
        

        // Prepare SQL query
        $query = "INSERT INTO Tickets (TicketID, NumberOfGuests, PNR, SecNum, NumberOfAdults, NumberOfChildren, ";
        for ($i = 1; $i <= min(5, $totalGuests); $i++) {
            $query .= "Name$i, Aadhar$i, Phone$i, ";
        }
        $query .= "Price, PlatformNumber, TicketDate, Junction) VALUES ('$ticketID', $totalGuests, '$PNR', '$secNum', " . ($totalGuests - $numChildren) . ", $numChildren, ";
        for ($i = 0; $i < min(5, $totalGuests); $i++) {
            $name = isset($guestNames[$i]) ? "'" . $guestNames[$i] . "'" : "NULL";
            $aadhar = isset($aadharNumbers[$i]) ? "'" . $aadharNumbers[$i] . "'" : "NULL";
            $phone = isset($phoneNumbers[$i]) ? "'" . $phoneNumbers[$i] . "'" : "NULL";
            $query .= "$name, $aadhar, $phone, ";
        }
        $query .= "$ticketPrice, $platformNumber, CURDATE(), '$destination')";


        // Execute query
        if (mysqli_query($con, $query)) {
            // Success message
            echo "<script>alert('Ticket successfully updated.'); window.location.href = 'payment.php';</script>";
        } else {
            // Error message
            echo "<script>alert('Error updating record: " . mysqli_error($con) . "');</script>";
        }
    }

    mysqli_close($con);
    mysqli_close($aadhar_con);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Input</title>
    <link rel="stylesheet" href="css/stylelg.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
          body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    overflow-x: hidden; /* Prevent horizontal scroll on smaller screens */
}

.container {
    padding: 40px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 900px;
    margin: 20px;
}

.guest-input {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    margin-bottom: 10px;
}

.guest-input label, .guest-input input {
    margin-right: 10px;
    flex: 1 1 100%;
    min-width: 100px; /* Ensure input fields don't shrink too small */
}

.guest-input input {
    min-width: 150px;
}

.submit-button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
}

.submit-button:hover {
    background-color: #45a049;
}

button[type="button"] {
    margin-right: 10px;
}

/* Responsive Styles */
@media (min-width: 768px) {
    .guest-input label, .guest-input input {
        flex: 1;
    }

    .guest-input {
        flex-wrap: nowrap;
    }
}
@media (max-width: 767px) {
    .container {
        width: 95%;
        padding: 20px;
    }

    .guest-input input {
        width: calc(100% - 20px); /* Adjust width to fit within flex container */
    }

    .guest-input label {
        flex-basis: 100%;
        margin-bottom: 5px; /* Adjust margin for better spacing */
    }
}
   
    </style>
</head>
<body>
    <header></header>
    <div class="container">
        <h1><center>Guest Information Form</center></h1>
        <form id="guestForm" method="post" action="">
            <div class="guest-input">
                <label for="guestName1">Guest Name:</label>
                <input type="text" id="guestName1" name="guestName1" placeholder="Enter guest name" required oninput="capitalizeWords(this)">
                <label for="guestAadhar1">Aadhar Number:</label>
                <input type="text" id="guestAadhar1" name="guestAadhar1" maxlength="14" placeholder="0000 0000 0000" required oninput="formatAadhar(this)">&nbsp&nbsp
                <label for="guestPhone1">Phone Number:</label>
                <input type="text" id="guestPhone1" name="guestPhone1" maxlength="15" placeholder="+91 00000 00000" required oninput="formatPhone(this)">
            </div>
            <div id="additionalGuests"></div><center>
            <button type="button" class="btn btn-primary" onclick="addGuest()">Add Guest</button> &nbsp; &nbsp;
            <button type="button" class="btn btn-primary" onclick="removeGuest()">Remove Guest</button> &nbsp; &nbsp;
            <input type="checkbox" id="applyToAll" name="applyToAll" onchange="applyToAllChanged()">&nbsp
            <label for="applyToAll">Apply to All</label></center>
            <hr><center>
            <div class="guest-input">
                <label for="numChildren">Number of Children:</label>
                <input type="number" id="numChildren" name="numChildren" min="0" max="5" required>
                <label for="destination">Junction:</label>
                <select id="destination" name="destination" aria-placeholder="SELECT" required>
                    <option value="">Select</option>
                    <option value="CHENNAI">CHENNAI</option>
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
            <div class="guest-input">
                <label for="platformNumber">Platform Number:</label>
                <input type="number" id="platformNumber" max="25" name="platformNumber" min="1" required>
            </div>
            <div class="guest-input">
                <label for="secNum">SecondaryNumber:</label>
                <input type="text" id="secNum" name="secNum"maxlength="15" placeholder="+91 00000 00000" required oninput="formatPhone(this)"></div><br><div><b>
                "Please Enter a secondary number for emergency purposes."</b>
            </div>
            <input type="hidden" id="guestCount" name="guestCount" value="1">
            <hr><center>
            <button type="submit" class="submit-button">Submit</button></center>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function(){
            document.getElementById("guestForm").addEventListener("submit", function (event) { 
                const numChildren = parseInt(document.getElementById("numChildren").value);
                const guestCount = parseInt(document.getElementById("guestCount").value);
                const platformNumber = parseInt(document.getElementById("platformNumber").value);
                const destination = document.getElementById("destination").value;

                if (numChildren >= guestCount) {
                    alert("Number of children cannot exceed or be equivalent to total number of guests");
                    event.preventDefault(); 
                }

                if (destination === "SALEM" && platformNumber > 6) {
                    alert("SALEM only has 6 platforms.");
                    event.preventDefault();
                } else if (destination === "KARUR" && platformNumber > 5) {
                    alert("KARUR only has 5 platforms.");
                    event.preventDefault();
                } 
                else if (destination === "CHENNAI" && platformNumber > 17) {
                    alert("CHENNAI only has 17 platforms.");
                    event.preventDefault();
                }  
                else if (destination === "DINDIGUL" && platformNumber > 5) {
                    alert("DINDIGUL only has 5 platforms.");
                    event.preventDefault();
                } else if (destination === "MADURAI" && platformNumber > 7) {
                    alert("MADURAI only has 7 platforms.");
                    event.preventDefault();
                } else if (destination === "VIRUDHUNAGAR" && platformNumber > 4) {
                    alert("VIRUDHUNAGAR only has 4 platforms.");
                    event.preventDefault();
                }
				  else if (destination === "COIMBATORE" && platformNumber > 6) {
                    alert("COIMBATORE only has 6 platforms.");
                    event.preventDefault();
                } else if (destination === "CHENGAIPATTU" && platformNumber > 9) {
                    alert("CHENGAIPATTU only has 9 platforms.");
                    event.preventDefault();
                } else if (destination === "TIRUPPUR" && platformNumber > 2) {
                    alert("TIRUPPUR only has 2 platforms.");
                    event.preventDefault();
                } else if (destination === "ERODE" && platformNumber > 4) {
                    alert("ERODEonly has 4 platforms.");
                    event.preventDefault();
                } else if (destination === "THENI" && platformNumber > 2) {
                    alert("THENI only has 2 platforms.");
                    event.preventDefault();
                }
            });
        });
        
        function addGuest() {
            let guestCount = parseInt(document.getElementById("guestCount").value);
            if (guestCount < 5) {
                guestCount++;
                document.getElementById("guestCount").value = guestCount;
                let additionalGuests = document.getElementById("additionalGuests");
                let guestDiv = document.createElement("div");
                guestDiv.classList.add("guest-input");
                guestDiv.innerHTML = `
                    <label for="guestName${guestCount}">Guest Name:</label>
                    <input type="text" id="guestName${guestCount}" name="guestName${guestCount}" placeholder="Enter guest name" oninput="capitalizeWords(this)">
                    <label for="guestAadhar${guestCount}">Aadhar Number:</label>
                    <input type="text" id="guestAadhar${guestCount}" name="guestAadhar${guestCount}" maxlength="14" placeholder="0000 0000 0000" oninput="formatAadhar(this)">
                    <label for="guestPhone${guestCount}">Phone Number:</label>
                    <input type="text" id="guestPhone${guestCount}" name="guestPhone${guestCount}" maxlength="15" placeholder="+91 00000 00000" oninput="formatPhone(this)">
                `;
                additionalGuests.appendChild(guestDiv);
            } else {
                alert("Maximum number of guests reached.");
            }
        }

        function removeGuest() {
            let guestCount = parseInt(document.getElementById("guestCount").value);
            if (guestCount > 1) {
                document.getElementById("guestCount").value = guestCount - 1;
                let additionalGuests = document.getElementById("additionalGuests");
                additionalGuests.removeChild(additionalGuests.lastChild);
            }
        }

        function capitalizeWords(input) {
            input.value = input.value.toLowerCase().replace(/\b\w/g, function (char) {
                return char.toUpperCase();
            });
        }

        function formatAadhar(input) {
            let value = input.value.replace(/\D/g, '').slice(0, 12);
            if (value.length > 4) {
                value = value.slice(0, 4) + ' ' + value.slice(4);
            }
            if (value.length > 9) {
                value = value.slice(0, 9) + ' ' + value.slice(9);
            }
            input.value = value;
        }

        function formatPhone(input) {
    var value = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    
    // Remove the country code if it exists
    if (value.startsWith('91')) {
        value = value.substring(2);
    }

    // Ensure the number has 10 digits
    if (value.length > 10) {
        value = value.substring(0, 10);
    }

    // Format the number
    var formattedValue = '+91 ' + value.substring(0, 5) + ' ' + value.substring(5);
    input.value = formattedValue;
}


        function applyToAllChanged() {
            let applyToAll = document.getElementById("applyToAll").checked;
            let guestCount = parseInt(document.getElementById("guestCount").value);
            for (let i = 1; i <= guestCount; i++) {
                let phoneField = document.getElementById(`guestPhone${i}`);
                if (applyToAll) {
                    phoneField.value = document.getElementById("guestPhone1").value;
                }
            }
        }
        document.getElementById("guestPhone1").addEventListener("input", function() {
            if (document.getElementById("applyToAll").checked) {
                const firstPhone = this.value;
                const guestPhoneInputs = document.querySelectorAll("[id^='guestPhone']");
                guestPhoneInputs.forEach((input, index) => {
                    if (index > 0) {
                        input.value = firstPhone;
                    }
                });
            }
        });
    </script>
</body>
</html>
