<!DOCTYPE html>
<html>
<head>
    <title>Select Guests for Hand Straps</title>
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>

<div class='container'>
    <div class='card'>
        <div class='header bg-primary text-white text-center'>
            Select Guests for Hand Straps
        </div>
        <div><b> "Please select all the children and senior citizens for HandStrap"</b>
</div>
        <form action='generateHandStrap.php' method='post'>
            <div class='card-body'>
                <?php
                $con = mysqli_connect("localhost", "your username", "your password", "train");

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT * FROM Tickets ORDER BY TicketDate DESC, TicketTime DESC LIMIT 1";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $guests = [
                        ['name' => $row["Name1"], 'phone' => $row["Phone1"]],
                        ['name' => $row["Name2"], 'phone' => $row["Phone2"]],
                        ['name' => $row["Name3"], 'phone' => $row["Phone3"]],
                        ['name' => $row["Name4"], 'phone' => $row["Phone4"]],
                        ['name' => $row["Name5"], 'phone' => $row["Phone5"]]
                    ];

                    foreach ($guests as $index => $guest) {
                        if (!empty($guest['name'])) {
                            echo "<div class='form-check'>";
                            echo "<input class='form-check-input' type='checkbox' name='guests[]' value='{$guest['name']}|{$guest['phone']}|{$row['Junction']}|{$row['PlatformNumber']}' id='guest{$index}'>";
                            echo "<label class='form-check-label' for='guest{$index}'>";
                            echo htmlspecialchars($guest['name']) . " - " . htmlspecialchars($guest['phone']);
                            echo "</label>";
                            echo "</div><br>";
                        }
                    }

                   
                    echo "<input type='hidden' name='ticketData' value='" . json_encode($row) . "'>";
                } else {
                    echo "<p>No guests found</p>";
                }

                $conn->close();
                ?>
            </div>
            <div class='card-footer text-center'>
                <button type='submit' class='btn btn-primary'>Generate Hand Straps</button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src='https://code.jquery.com/jquery-3.5.1.slim.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js'></script>
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>

</body>
</html>
