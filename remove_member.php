<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Member</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php
    include 'header.php';
    include './io/databaseHandle.php';

    ////INCLUDE THIS NEXT LINE TO LOCK A PAGE////
    //include './verif/content-restrict.php';
    ?>

    <h1>Remove Member</h1>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //recieving data from form
        $username = $_POST["username"];

        //handle
        $query = "DELETE FROM mysql_schema.people WHERE username = '$username'";
        $queryAttempt = $socket->execute_query($query, null);

        if (!$queryAttempt) {
            echo '<script>console.log("<debug> User removal failed"); </script>';
        } else {
            echo '<script>console.log("<debug> User removed successfully"); </script>';
        }
    }
    ?>

    <form action="" method="POST">
        <label for="username">Username to Remove:</label>
        <input type="text" name="username" id="username" required><br>

        <input type="submit" value="Remove Member">
        <br>
    </form>

    <?php
    //displaying table with member details
    $querySelectMembers = "SELECT * FROM mysql_schema.people";
    $result = $socket->execute_query($querySelectMembers, null);
    
    if ($result) {
        echo "<h2>Member Details:</h2>";
        echo "<table>";
        echo "<tr><th>UUID</th><th>Username</th></th><th>Given Name</th><th>Surname</th><th>User Email</th></tr>";
    
        foreach ($result as $row) {
            echo "<tr>";
            echo "<td>{$row['UUID']}</td>";
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
