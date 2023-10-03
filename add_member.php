<?php
// SSL/TLS configuration
$mysqli = new mysqli();
mysqli_ssl_set($mysqli, NULL, NULL, "cert.pem", NULL, NULL);

// Database configuration
$host = "gotogro-mrm-db.mysql.database.azure.com";
$username = "mydemouser";
$password = "Vsp3dbwH";
$database = "mysql_schema";
$port = 3306;

// Create a database connection with SSL/TLS
if (!$mysqli->real_connect($host, $username, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check the connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staffType = $_POST["staffType"];
    $username = $_POST["username"];
    $surname = $_POST["surname"];
    $givenName = $_POST["givenName"];
    $pwdHash = $_POST["pwdHash"];
    $userEmail = $_POST["userEmail"];

    // SQL query to insert member data into the "people" table
    $sql = "INSERT INTO people (staffType, username, surname, givenName, pwdHash, userEmail) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);

    // Bind parameters
    $stmt->bind_param("isssss", $staffType, $username, $surname, $givenName, $pwdHash, $userEmail);

    // Execute the query
    if ($stmt->execute()) {
        // Insertion successful
        echo "Member added successfully!";
    } else {
        // Insertion failed
        echo "Error: " . $mysqli->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Member</title>
    <link rel="stylesheet" type="text/css" href="styles.css"> <!-- Link to external CSS file -->
</head>
<body>
<?php include 'header.php'; ?>
    <?php include 'menu.php'; ?>
    <div class="container">
        <h2>Add New Member</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="staffType">Staff Type:</label>
            <input type="text" id="staffType" name="staffType" required>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="surname">Surname:</label>
            <input type="text" id="surname" name="surname" required>
            <label for="givenName">Given Name:</label>
            <input type="text" id="givenName" name="givenName" required>
            <label for="pwdHash">Password Hash:</label>
            <input type="text" id="pwdHash" name="pwdHash" required>
            <label for="userEmail">User Email:</label>
            <input type="text" id="userEmail" name="userEmail" required>
            <input type="submit" value="Add Member">
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
