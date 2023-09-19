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

    // Database connection details
    $db_host = "gotogro-mrm-db.mysql.database.azure.com";
    $db_username = "mydemouser";
    $db_password = "Vsp3dbwH";
    $db_name = "mysql_schema";

    // Create a database connection
    $conn = new mysqli($db_host, $db_username, $db_password, $db_name);

    // Check for connection errors
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert the member details into the database
    $sql = "INSERT INTO members (first_name, last_name, email, address, mobile_number, user_id, is_staff) 
            VALUES ('$first_name', '$last_name', '$email', '$address', '$mobile_number', '$user_id', $is_staff)";

    if ($conn->query($sql) === TRUE) {
        echo "Member details added successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>
