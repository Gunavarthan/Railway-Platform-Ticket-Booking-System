<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My train</title>
    <link rel="stylesheet" href="stylelg.css">
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
        <h2>Enter Your PNR</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="PNR">PNR:</label>
                <input type="text" id="PNR" name="PNR" required>                <!--Getting inputs for the PNR-->
            </div>
            <div class="form-group">
                <button type="submit">Submit</button>
            </div>
        </form>
        <?php
            $con = mysqli_connect("localhost", "root", "guna", "train");

            if (isset($_POST["PNR"])) {
                $PNR = mysqli_real_escape_string($con, $_POST["PNR"]);
                $query = "SELECT * FROM pnr WHERE PNR='$PNR'";                   //selecting rows with same PNR value
                $result = mysqli_query($con, $query);

                if (mysqli_num_rows($result) > 0) {
                    echo "<script>window.location.href='ticketinput.php';</script>";    //if Result rows is greater than 0 then Redirect to Next Page
                } else {
                    echo "<div class='alert'>PNR not found</div>";              //Else return a Error Message
                }
            }

            mysqli_close($con);
        ?>
    </div>
</body>
</html>
