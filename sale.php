<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $transaction_id = $_POST["transaction_id"];
    $product_name = $_POST["product_name"];
    $quantity = $_POST["quantity"];
    $price = $_POST["price"];

    // Replace this with your database connection and query
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

    // Configure SSL options
    $mysqli->ssl_set(
        'path/to/client-key.pem', // Path to your client key file
        'path/to/client-cert.pem', // Path to your client certificate file
        'path/to/ca-cert.pem', // Path to your CA certificate file
        null, // Path to your server certificate file (if needed)
        null
    );

    // Establish the connection using SSL
    if (!$mysqli->real_connect($servername, $username, $password, $database)) {
        die("Connection failed: " . $mysqli->connect_error);
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="add_sales_record">
    <title>Add Sales Record</title>
</head>
<body>
    <h1>Add Sales Record</h1>
    <form action="sale.php" method="POST">
        <label for="transaction_id">Transaction ID:</label>
        <input type="text" name="transaction_id" id="transaction_id" readonly>
        <br>
        <label for="product_name">Product Name:</label>
        <input type="text" name="product_name" id="product_name" required>
        <br>
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" required>
        <br>
        <label for="price">Price per Unit:</label>
        <input type="number" name="price" id="price" required>
        <br>
        <input type="submit" value="Add Sale Record">
    </form>
    <script>
        document.getElementById("transaction_id").value = generateTransactionID();
        function generateTransactionID() {
            return 'TX' + Math.floor(Math.random() * 1000000);
        }
    </script>
</body>
</html>
