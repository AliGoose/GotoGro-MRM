<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php
    include 'header.php';
    include 'menu.php';
    include './io/databaseHandle.php'; // Include the database handle
    ?>

    <h1>Staff Log-in</h1>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve data from the form
        $username = $_POST["username"];
        $enteredPassword = $_POST["pwd"];

        $query = "SELECT * FROM mysql_schema.people WHERE username = '$username'";
        $queryAttempt = $socket->execute_query($query, null);

        if ($queryAttempt) {
          $passwordCmp = password_hash($enteredPassword, PASSWORD_BCRYPT);

          $result = mysqli_fetch_row($queryAttempt)[5];
          
          if(password_verify($enteredPassword, $result)){
            $_SESSION["user"]= $username;
            echo "<p>Successfully logged in as $username.</p>";
          } else {
            echo "<p>Wrong password.</p>";
          }
        } else {
          echo "<p>User does not exist.</p>";
        }
    }
    ?>

    <form action="" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br>

        <label for="pwd">Password:</label>
        <input type="password" name="pwd" id="pwd" required><br>

        <input type="submit" value="Log In">
        <br>
    </form>

    <?php include 'footer.php'; ?>
</body>
</html>
