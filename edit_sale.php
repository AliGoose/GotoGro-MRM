<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $transaction_id = $_POST["transaction_id"];
    $new_product_name = $_POST["product_name"];
    $new_quantity = $_POST["quantity"];
    $new_price = $_POST["price"];

    // Replace this with your database connection and query
<<<<<<< Updated upstream
    $db_host = "your_db_host";
    $db_username = "your_db_username";
    $db_password = "your_db_password";
    $db_name = "your_db_name";
=======
    $db_host = "gotogro-mrm-db.mysql.database.azure.com";
    $db_username = "mydemouser";
    $db_password = "Vsp3dbwH";
    $db_name = "mysql_schema";
>>>>>>> Stashed changes

    // Create a database connection
    $conn = new mysqli($db_host, $db_username, $db_password, $db_name);

    // Check for connection errors
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update the sale record in the database
    $sql = "UPDATE sales SET product_name='$new_product_name', quantity=$new_quantity, price=$new_price WHERE transaction_id='$transaction_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Sale record edited successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>
