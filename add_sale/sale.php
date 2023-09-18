<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $transaction_id = $_POST["transaction_id"];
    $product_name = $_POST["product_name"];
    $quantity = $_POST["quantity"];
    $price = $_POST["price"];

    // Replace this with your database connection and query
    $db_host = "your_db_host";
    $db_username = "your_db_username";
    $db_password = "your_db_password";
    $db_name = "your_db_name";

    // Create a database connection
    $conn = new mysqli($db_host, $db_username, $db_password, $db_name);

    // Check for connection errors
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert the sales record into the database
    $sql = "INSERT INTO sales (transaction_id, product_name, quantity, price) VALUES ('$transaction_id', '$product_name', $quantity, $price)";

    if ($conn->query($sql) === TRUE) {
        echo "Sales record added successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>
