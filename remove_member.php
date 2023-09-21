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
    include 'menu.php';
    ?>

    <h1>Remove Member</h1>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve data from the form
        $user_id_to_remove = $_POST["user_id_to_remove"];

        $servername = "gotogro-mrm-db.mysql.database.azure.com";
        $username = "mydemouser";
        $password = "Vsp3dbwH";
        $database = "mysql_schema";

        // Create a connection object with SSL options
        $mysqli = new mysqli($servername, $username, $password, $database, 3306, null, MYSQLI_CLIENT_SSL);

        // Check for connection errors
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        // Configure SSL options
        $mysqli->ssl_set(
            'https://github.com/AliGoose/GotoGro-MRM/blob/main/DigiCertGlobalRootCA.crt.pem',
            null,
            null,
            null,
            null
        );

        // Establish the connection using SSL
        if (!$mysqli->real_connect($servername, $username, $password, $database)) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        // Remove the member from the database
        $sql = "DELETE FROM members WHERE user_id = '$user_id_to_remove'";

        if ($mysqli->query($sql) === TRUE) {
            echo "Member removed successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }

        // Close the connection
        $mysqli->close();
    }
    ?>

    <form action="" method="POST">
        <label for="user_id_to_remove">User ID to Remove:</label>
        <input type="text" name="user_id_to_remove" id="user_id_to_remove" required><br>

        <input type="submit" value="Remove Member">
    </form>

    <?php
    // Retrieve and display member details (optional)
    $servername = "gotogro-mrm-db.mysql.database.azure.com";
    $username = "mydemouser";
    $password = "Vsp3dbwH";
    $database = "mysql_schema";

    $mysqli = new mysqli($servername, $username, $password, $database, 3306, null, MYSQLI_CLIENT_SSL);

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $mysqli->ssl_set(
        'https://github.com/AliGoose/GotoGro-MRM/blob/main/DigiCertGlobalRootCA.crt.pem',
        null,
        null,
        null,
        null
    );

    if (!$mysqli->real_connect($servername, $username, $password, $database)) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $result = $mysqli->query("SELECT * FROM members");

    if ($result->num_rows > 0) {
        echo "<h2>Member Details:</h2><ul>";
        while($row = $result->fetch_assoc()) {
            echo "<li>" . $row["first_name"] . " " . $row["last_name"] . " - Email: " . $row["email"] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "No members found.";
    }

    $mysqli->close();
    ?>

    <?php include 'footer.php'; ?>
</body>
</html>
