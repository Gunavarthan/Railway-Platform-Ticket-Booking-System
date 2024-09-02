<?php
$con = mysqli_connect("localhost", "your username", "your password", "train");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["AddAdmin"])) {
    $user = mysqli_real_escape_string($con, $_POST['username']);
    $pass0 = mysqli_real_escape_string($con, $_POST['password0']);
    $pass1 = mysqli_real_escape_string($con, $_POST['password1']);

    if ($pass0 === $pass1) {
        // Query to insert user credentials
        $query = "INSERT INTO users (username, password) VALUES ('$user', '$pass1')";
        if (mysqli_query($con, $query)) {
            header("Location: admin.php?success=1");
            exit();
        } else {
            header("Location: admin.php?error=1");
            error_log("Error: " . mysqli_error($con));
            exit();
        }
    } else {
        header("Location: addAdmin.php?password_mismatch=1");
        exit();
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo.png">
    <link rel="stylesheet" href="css/style.css">
    <title>My Train</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>New Admin</h2>
        <form action="addAdmin.php" method="POST">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="password" id="password0" name="password0" placeholder="Password" required>
            <input type="password" id="password1" name="password1" placeholder="Confirm Password" required>
            <button id="AddAdmin" type="submit" name="AddAdmin">Add Admin</button>
        </form>
    </div>

    <script>
        window.onload = function() {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('password_mismatch')) {
                alert("Passwords do not match.");
            }
        };
    </script>
</body>
</html>
