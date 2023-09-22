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
	$certificate = 'cert/DigiCertGlobalRootCA.crt.pem';

    // Create a database connection
    $conn = new mysqli($db_host, $db_username, $db_password, $db_name3306, MYSQLI_CLIENT_SSL);

    // Check for connection errors
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Configure SSL options with the certificate file in the same directory
	$mysqli->ssl_set(
        $certificate,
        null,
        null,
        null,
        null
    );

    // Establish the connection using SSL
    if (!$conn->real_connect($db_host, $db_username, $db_password, $db_name)) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the sale record exists
    $check_sql = "SELECT * FROM sales WHERE transaction_id='$transaction_id'";
    $result = $conn->query($check_sql);

    if ($result->num_rows == 0) {
        echo "Sale record with Transaction ID '$transaction_id' does not exist.";
    } else {
        // Update the sale record in the database
        $update_sql = "UPDATE sales SET product_name='$product_name', quantity=$quantity, price=$price WHERE transaction_id='$transaction_id'";

        if ($conn->query($update_sql) === TRUE) {
            echo "Sale record updated successfully!";
        } else {
            echo "Error updating sale record: " . $conn->error;
        }
    }
	
	// Query to retrieve sales records
    $sql = "SELECT * FROM sales";

    // Execute the query
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Display table header
        echo "<table border='1'>";
        echo "<tr><th>Transaction ID</th><th>Product Name</th><th>Quantity</th><th>Price</th></tr>";

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["transaction_id"] . "</td><td>" . $row["product_name"] . "</td><td>" . $row["quantity"] . "</td><td>" . $row["price"] . "</td></tr>";
        }

        echo "</table>";
    } else {
        echo "No sales records found.";
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="edit_sales_record">
    <title>Edit Sales Record</title>
</head>
<body>
	<?php include 'header.php'; ?>
	<?php include 'menu.php';   ?>
    <h1>Edit Sales Record</h1>
    <form action="edit_sale.php" method="POST">
        <label for="transaction_id">Transaction ID:</label>
        <input type="text" name="transaction_id" id="transaction_id" required>
        <br>
        <label for="product_name">New Product Name:</label>
        <input type="text" name="product_name" id="product_name" required>
        <br>
        <label for="quantity">New Quantity:</label>
        <input type="number" name="quantity" id="quantity" required>
        <br>
        <label for="price">New Price per Unit:</label>
        <input type="number" name="price" id="price" required>
        <br>
        <input type="submit" value="Edit Sale Record">
    </form>
	<?php
    include 'footer.php';
    ?>
</body>
</html>
