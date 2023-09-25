<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Member Details</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php
    include 'header.php';
    include 'menu.php';
    ?>

    <h1>Add Member Details</h1>

    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        include './io/databaseHandle.php';
        
        // Retrieve data from form submission
        $prefUsername = $_POST["prefUsername"];
        $first_name = $_POST["first_name"];
        $last_name = $_POST["last_name"];
        $email = $_POST["email"];
        // $address = $_POST["address"];
        $mobile_number = $_POST["mobile_number"];
        $passwordHash = password_hash($_POST["userPassword"], PASSWORD_BCRYPT);
        
        
        $queryCommitMember = "insert into mysql_schema.people values (DEFAULT, DEFAULT, '$prefUsername', '$first_name', '$last_name', '$passwordHash', '$email')";
        $queryAttempt= $socket->execute_query($queryCommitMember, null);

        if (!$queryAttempt){
            echo '<script>console.log("<debug> User commit failed"); </script>';
        } else {
            echo '<script>console.log("<debug> User commit succeeded"); </script>';
        }

        $socket->close();
        }
    ?>

    <form action="" method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" required><br>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br>

        <!-- <label for="address">Address:</label>
        <input type="text" name="address" id="address" required><br> -->

        <label for="mobile_number">Mobile Number:</label>
        <input type="tel" name="mobile_number" id="mobile_number" required><br>

        <label for="username">Preferred Username:</label>
        <input type="text" name="prefUsername" id="prefUsername" required><br>

        <label for="userPassword">Password:</label>
        <input type="password" name="userPassword" id="userPassword" required><br>


        <input type="submit" value="Add Member">
    </form>


    <?php include 'footer.php'; ?>
</body>
</html>
