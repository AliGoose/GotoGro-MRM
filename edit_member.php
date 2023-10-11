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


// Initialize member details
$staffType = "";
$username = "";
$surname = "";
$givenName = "";
$pwdHash = "";
$userEmail = "";
$editId = null;

// Check if member data should be loaded for editing
if (isset($_GET['edit_id'])) {
    $editId = $_GET['edit_id'];
    // Query to retrieve member data by ID
    $query = "SELECT * FROM people WHERE UUID = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $staffType = $row['staffType'];
        $username = $row['username'];
        $surname = $row['surname'];
        $givenName = $row['givenName'];
        $pwdHash = $row['pwdHash'];
        $userEmail = $row['userEmail'];
    }
    $stmt->close();
}

// Process the form submission for editing or adding
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $editId = $_POST["edit_id"];
    $staffType = $_POST["staffType"];
    $username = $_POST["username"];
    $surname = $_POST["surname"];
    $givenName = $_POST["givenName"];
    $pwdHash = $_POST["pwdHash"];
    $userEmail = $_POST["userEmail"];
    
    // Validate staff type (only accept specific staff types)
    $validStaffTypes = [1,2]; // Add the valid staff types here
    if (!in_array($staffType, $validStaffTypes)) {
        die("Invalid staff type.");
    }

    // Validate email format
    if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Check if the username already exists
    $checkUsernameQuery = "SELECT UUID FROM people WHERE username = ?";
    $checkStmt = $mysqli->prepare($checkUsernameQuery);
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Username already exists, retrieve the UUID
        $row = $checkResult->fetch_assoc();
        $existingUUID = $row['UUID'];

        // Perform an UPDATE operation
        $sql = "UPDATE people SET staffType = ?, username = ?, surname = ?, givenName = ?, pwdHash = ?, userEmail = ? WHERE UUID = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("isssssi", $staffType, $username, $surname, $givenName, $pwdHash, $userEmail, $existingUUID);

        if ($stmt->execute()) {
            // Update successful
            echo "Member details updated successfully!";
        } else {
            // Update failed
            echo "Error: " . $mysqli->error;
        }
    } else {
        if ($editId !== null) {
            // The username doesn't exist, but this is an edit operation
            echo "Error: Username not found for editing.";
        } else {
            // Username doesn't exist, proceed with INSERT operation
            $insertSql = "INSERT INTO people (staffType, username, surname, givenName, pwdHash, userEmail) VALUES (?, ?, ?, ?, ?, ?)";
            $insertStmt = $mysqli->prepare($insertSql);
            $insertStmt->bind_param("isssss", $staffType, $username, $surname, $givenName, $pwdHash, $userEmail);

            if ($insertStmt->execute()) {
                // Insertion successful
                echo "Member added successfully!";
            } else {
                // Insertion failed
                echo "Error: " . $mysqli->error;
            }
        }
    }

    // Close the statement
    $checkStmt->close();
}

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member Details</title>
    <link rel="stylesheet" type="text/css" href="styles.css"> <!-- Link to external CSS file -->
</head>
<body>
<?php 
include 'header.php'; 
include 'menu.php'; 
 include './io/databaseHandle.php'; // Include the database handle

 
    ////INCLUDE THIS NEXT LINE TO LOCK A PAGE////
    include './verif/content-restrict.php';
    ?>
    <div class="container">
        <h2>Edit Member Details</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="hidden" name="edit_id" value="<?php echo $editId; ?>">
            <label for="staffType">Staff Type:</label>
            <input type="text" id="staffType" name="staffType" value="<?php echo $staffType; ?>" required>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
            <label for="surname">Surname:</label>
            <input type="text" id="surname" name="surname" value="<?php echo $surname; ?>" required>
            <label for="givenName">Given Name:</label>
            <input type="text" id="givenName" name="givenName" value="<?php echo $givenName; ?>" required>
            <label for="pwdHash">Password Hash:</label>
            <input type="text" id="pwdHash" name="pwdHash" value="<?php echo $pwdHash; ?>" required>
            <label for="userEmail">User Email:</label>
            <input type="text" id="userEmail" name="userEmail" value="<?php echo $userEmail; ?>" required>
            <input type="submit" value="Update Details">
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>

