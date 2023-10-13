<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Member</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php
    include 'header.php';
    //include 'menu.php';
    include './io/databaseHandle.php';

    ////INCLUDE THIS NEXT LINE TO LOCK A PAGE////
    //include './verif/content-restrict.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $staffType = $_POST["staffType"];
        $username = $_POST["username"];
        $surname = $_POST["surname"];
        $givenName = $_POST["givenName"];
        $pwd = password_hash($_POST["pwd"], PASSWORD_BCRYPT);
        $userEmail = $_POST["userEmail"];

        // Use the database handle to insert member details
        $query = "INSERT INTO mysql_schema.people (staffType, username, surname, givenName, pwdHash, userEmail) 
                  VALUES ('$staffType', '$username', '$surname', '$givenName', '$pwd', '$userEmail')";
        $queryAttempt = $socket->execute_query($query, null);

        if (!$queryAttempt) {
            echo '<script>console.log("<debug> User addition failed"); </script>';
        } else {
            echo '<script>console.log("<debug> User added successfully"); </script>';
        }
    }
    ?>

    <h1>Add Member</h1>

    <form action="" method="POST">
        <label for="staffType">Staff Type:</label>
        <input type="text" name="staffType" id="staffType" required><br>

        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br>

        <label for="surname">Surname:</label>
        <input type="text" name="surname" id="surname" required><br>

        <label for="givenName">Given Name:</label>
        <input type="text" name="givenName" id="givenName" required><br>

        <label for="pwd">Password:</label>
        <input type="password" name="pwd" id="pwd" required><br>

        <label for="userEmail">User Email:</label>
        <input type="email" name="userEmail" id="userEmail" required><br>

        <input type="submit" value="Add Member">
        <br>
    </form>

    <?php
    $querySelectMembers = "SELECT * FROM mysql_schema.people";
    $result = $socket->execute_query($querySelectMembers, null);
    
    if ($result) {
        echo "<h2>Member Details:</h2>";
        echo "<table>";
        echo "<tr><th>UUID</th><th>Staff Type</th><th>Username</th></th><th>Given Name</th><th>Surname</th><th>User Email</th></tr>";
    
        foreach ($result as $row) {
            echo "<tr>";
            echo "<td>{$row['UUID']}</td>";
            echo "<td>{$row['staffType']}</td>";
            echo "<td>{$row['username']}</td>";
            echo "<td>{$row['givenName']}</td>";
            echo "<td>{$row['surname']}</td>";
            echo "<td>{$row['userEmail']}</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No members found.</p>";
    }

    // Close the database connection
    $socket->close();
    ?>

    <?php include 'footer.php'; ?>
</body>
</html>
