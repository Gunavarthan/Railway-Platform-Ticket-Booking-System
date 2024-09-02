<?php
$con = mysqli_connect("localhost", "your username", "your password", "train");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["Login"])|| isset($_POST["AddAdmin"])) {
    // Fetching user input
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Query to check user credentials
    $query = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
    $result = mysqli_query($con,$query);

    if (mysqli_num_rows($result) > 0 && isset($_POST["Login"])) {
        header("Location: search.php");
    }elseif(mysqli_num_rows($result) > 0 && isset($_POST["AddAdmin"]))
    {
        header("Location: addAdmin.php");
    } 
    else {
        header("Location: admin.php?error=1");
    }

}
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
        <h2>Admin Login</h2>
        <form action="admin.php" method="POST">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <label>
                <input type="checkbox" id="show-password"> Show Password
            </label>
            <button id="login" type="submit" name ="Login">Login</button>
            <button id="AddAdmin" type="submit" name ="AddAdmin">Add Admin</button>
            <!-- <p><a href="forget.php">Forget Password</a></p> -->
        </form>
    </div>

    <script>
        document.getElementById('show-password').addEventListener('change', function() {
            var passwordField = document.getElementById('password');
            passwordField.type = this.checked ? 'text' : 'password';
        });

        window.onload = function() {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                alert("Invalid username or password");
            }else if (urlParams.has('success')) {
                alert("New admin added successfully.");
            }
        };
    </script>
</body>
</html>
