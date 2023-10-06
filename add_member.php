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

    // Check if the username already exists in the database
    $checkQuery = "SELECT COUNT(*) as count FROM people WHERE username = ?";
    $checkStmt = $mysqli->prepare($checkQuery);
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $row = $checkResult->fetch_assoc();
    $existingUsernameCount = $row["count"];
    $checkStmt->close();

    if ($existingUsernameCount > 0) {
        echo "Username already exists. Please choose a different username.";
    } else {
        // Validate staff type (only accept specific staff types)
        $validStaffTypes = [1, 2]; // Add the valid staff types here
        if (!in_array($staffType, $validStaffTypes)) {
            die("Invalid staff type.");
        }

        // Validate email format
        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            die("Invalid email format.");
        }

        // SQL query to insert member data into the "people" table
        $insertQuery = "INSERT INTO people (staffType, username, surname, givenName, pwdHash, userEmail) VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = $mysqli->prepare($insertQuery);

        // Bind parameters
        $insertStmt->bind_param("isssss", $staffType, $username, $surname, $givenName, $pwdHash, $userEmail);

        // Execute the query
        if ($insertStmt->execute()) {
            // Insertion successful
            echo "Member added successfully!";
        } else {
            // Insertion failed
            echo "Error: " . $mysqli->error;
        }

        // Close the statement
        $insertStmt->close();
    }
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
