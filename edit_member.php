<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php
    include 'header.php';
    //include 'menu.php';
    include './io/databaseHandle.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        
        // Check if the username exists in the database
        $queryCheckUser = "SELECT * FROM mysql_schema.people WHERE username='$username'";
        $resultCheckUser = $socket->execute_query($queryCheckUser, null);

        if ($resultCheckUser && mysqli_num_rows($resultCheckUser) > 0) {
            $row = mysqli_fetch_assoc($resultCheckUser);
            $uuid = $row['UUID'];
            $surname = $_POST["surname"];
            $givenName = $_POST["givenName"];
            $pwd = password_hash($_POST["pwd"], PASSWORD_BCRYPT);
            $userEmail = $_POST["userEmail"];

            // Use the database handle to update member details
            $query = "UPDATE mysql_schema.people 
                      SET surname='$surname', givenName='$givenName', pwdHash='$pwd', userEmail='$userEmail' 
                      WHERE username='$username'";
            $queryAttempt = $socket->execute_query($query, null);

            if ($queryAttempt) {
                echo '<script>console.log("<debug> User updated successfully"); </script>';
            } else {
                echo '<script>console.log("<debug> User update failed"); </script>';
            }
        } else {
            echo "No user found with the provided username.";
        }
    }
    ?>

    <h1>Edit Member</h1>

    <form action="" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br>

        <!-- Fields for editing -->
        <label for="surname">Surname:</label>
        <input type="text" name="surname" id="surname" required><br>

        <label for="givenName">Given Name:</label>
        <input type="text" name="givenName" id="givenName" required><br>

        <label for="pwd">Password:</label>
        <input type="password" name="pwd" id="pwd" required><br>

        <label for="userEmail">User Email:</label>
        <input type="email" name="userEmail" id="userEmail" required><br>

        <input type="submit" value="Edit Member">
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