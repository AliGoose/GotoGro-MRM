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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
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
</body>
</html>
