<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $member_id = $_POST["member_id"];
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

    // Update member details in the database
    $sql = "UPDATE members 
            SET first_name='$first_name', last_name='$last_name', email='$email', address='$address', 
                mobile_number='$mobile_number', user_id='$user_id', is_staff=$is_staff 
            WHERE id = $member_id";

    if ($conn->query($sql) === TRUE) {
        echo "Member details updated successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>
