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
        // Retrieve data from the form
        $first_name = $_POST["first_name"];
        $last_name = $_POST["last_name"];
        $email = $_POST["email"];
        $address = $_POST["address"];
        $mobile_number = $_POST["mobile_number"];
        $user_id = $_POST["user_id"];
        $is_staff = isset($_POST["is_staff"]) ? 1 : 0; // Checkbox handling

        $servername = "gotogro-mrm-db.mysql.database.azure.com";
        $username = "mydemouser";
        $password = "Vsp3dbwH";
        $database = "mysql_schema";

        $certificate = 'cert/DigiCertGlobalRootCA.crt.pem';
        $mysqli = new mysqli($servername, $username, $password, $database, 3306, MYSQLI_CLIENT_SSL);
    
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }
    
        $mysqli->ssl_set(
            $certificate,
            null,
            null,
            null,
            null
        );
    

        // Establish the connection using SSL
        if (!$mysqli->real_connect($servername, $username, $password, $database)) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        // Insert the member details into the database
        $sql = "INSERT INTO members (first_name, last_name, email, address, mobile_number, user_id, is_staff) 
                VALUES ('$first_name', '$last_name', '$email', '$address', '$mobile_number', '$user_id', $is_staff)";

        if ($mysqli->query($sql) === TRUE) {
            echo "Member details added successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }

        // Close the connection
        $mysqli->close();
    }
    ?>

    <form action="" method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" required><br>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br>

        <label for="address">Address:</label>
        <input type="text" name="address" id="address" required><br>

        <label for="mobile_number">Mobile Number:</label>
        <input type="tel" name="mobile_number" id="mobile_number" required><br>

        <label for="user_id">User ID:</label>
        <input type="text" name="user_id" id="user_id" required><br>

        <label for="is_staff">Staff:</label>
        <input type="checkbox" name="is_staff" id="is_staff"><br>

        <input type="submit" value="Add Member">
    </form>

    <?php
    // Retrieve and display member details
    $servername = "gotogro-mrm-db.mysql.database.azure.com";
    $username = "mydemouser";
    $password = "Vsp3dbwH";
    $database = "mysql_schema";

    $certificate = 'cert/DigiCertGlobalRootCA.crt.pem';
    $mysqli = new mysqli($servername, $username, $password, $database, 3306, MYSQLI_CLIENT_SSL);

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $mysqli->ssl_set(
        $certificate,
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
