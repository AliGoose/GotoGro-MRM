<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goto-Gro MRM</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'menu.php'; ?>
    <?php
    $conn = mysqli_init();
    mysqli_ssl_set($conn,NULL,NULL, "DigiCertGlobalRootCA.crt.pem", NULL, NULL);
    mysqli_real_connect($conn, "gotogro-mrm-db.mysql.database.azure.com", "mydemouser", "Vsp3dbwH", "mysql_schema", 3306, MYSQLI_CLIENT_SSL);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Define your SQL query
    $sql = "SELECT * FROM people";

    // Execute the query
    $result = $conn->query($sql);

    // Check if there are any rows in the result
    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            // You can format and print the data here
            echo "ID: " . $row["UUID"]. " - Username: " . $row["username"]. " - Email: " . $row["userEmail"]. "<br>";
        }
    } else {
        echo "<p>0 results</p>";
    }

    // Close the connection
    $conn->close();
    ?>



    <?php include 'footer.php'; ?>
</body>
</html>
