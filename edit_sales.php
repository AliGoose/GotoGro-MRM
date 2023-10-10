<?php
// Database connection
$serverURL = "gotogro-mrm-db.mysql.database.azure.com";
$username = "mydemouser";
$password = "Vsp3dbwH";
$database = "mysql_schema";
$certificate = './cert.pem';

$socket = mysqli_init();
mysqli_ssl_set($socket, NULL, NULL, $certificate, NULL, NULL);
$connectionState = mysqli_real_connect($socket, $serverURL, $username, $password, $database, 3306, MYSQLI_CLIENT_SSL);

if (!$connectionState) {
    echo '<script>console.log("<debug> sqli connection failed"); </script>';
    die('Failed to connect to MySQL: ' . mysqli_connect_error());
} else {
    echo '<script>console.log("<debug> sqli socket established"); </script>';
}



// Initialize variables
$username = "";
$editedSalesData = [];

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $username = $_POST["username"];

    if (isset($_POST["editSales"])) {
        // Edit Sales Record

        // Retrieve edited sales record data from the form
        $editedSalesData = $_POST["editedSalesData"];

        // Check if the username exists in the 'people' table
        $queryCheckUsername = "SELECT COUNT(*) FROM mysql_schema.people WHERE username = ?";

        $stmtCheckUsername = $socket->prepare($queryCheckUsername);
        $stmtCheckUsername->bind_param("s", $username);
        $stmtCheckUsername->execute();
        $stmtCheckUsername->bind_result($userExists);
        $stmtCheckUsername->fetch();
        $stmtCheckUsername->close();

        if ($userExists) {
            // Username exists, proceed with updating the sales record
            // You can implement the code to update the sales record here
            // For example, you can loop through the editedSalesData array and update the corresponding records in the database

            // Make sure to handle any errors or validation as needed

            // Display a success message if the update is successful
            echo '<div style="color: green;">Sales record updated successfully</div>';
        } else {
            // Username does not exist, display an error message on the page
            echo '<div style="color: red;">Username does not exist. Please check the username.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sales Record</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'menu.php'; ?>
    <h1>Edit Sales Record</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <!-- Edit Sales Data -->
        <h2>Edit Sales Data</h2>
        <div id="editedSalesData">
            <!-- You can dynamically generate input fields for editing sales data here -->
            <!-- Example input fields -->
            <div class="editedSalesItem">
                <label for="product1">Product Name:</label>
                <input type="text" name="editedSalesData[product1][name]" required>
                <label for="quantity1">Quantity:</label>
                <input type="number" name="editedSalesData[product1][quantity]" required>
            </div>
            <div class="editedSalesItem">
                <label for="product2">Product Name:</label>
                <input type="text" name="editedSalesData[product2][name]" required>
                <label for="quantity2">Quantity:</label>
                <input type="number" name="editedSalesData[product2][quantity]" required>
            </div>
        </div>

        <button type="button" onclick="addEditedSalesItem()">Add Sales Item</button>

        <input type="submit" name="editSales" value="Edit Sales Record">
    </form>
    <?php
    // Retrieve and display member details (optional)
    $querySelectTransaction = "SELECT * FROM mysql_schema.storetransactions";
    $result = $socket->query($querySelectTransaction);

    if ($result) {
    echo "<h2>Transaction Details:</h2>";
    echo "<table>";
    echo "<tr><th>TransactionID</th><th>Username</th><th>Product</th><th>Quantity</th><th>Price</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['transactionID']}</td>";
        echo "<td>{$row['username']}</td>";

        // Unserialize the product data
        $stockDataSerialized = $row['stockIDs_serialized'];
        $stockData = unserialize($stockDataSerialized);

        // Initialize variables to store product, quantity, and price information
        $productInfo = "";
        $quantityInfo = "";
        $priceInfo = "";

        foreach ($stockData as $stock) {
            $productName = $stock['name'];
            $quantity = $stock['quantity'];

            // Fetch the unit price for the product from the inventory table
            $queryUnitPrice = "SELECT unitPrice FROM mysql_schema.inventory WHERE productName = ?";
            $stmtUnitPrice = $socket->prepare($queryUnitPrice);
            $stmtUnitPrice->bind_param("s", $productName);
            $stmtUnitPrice->execute();
            $stmtUnitPrice->bind_result($unitPrice);

            if ($stmtUnitPrice->fetch()) {
                $price = $quantity * $unitPrice;
                $priceInfo .= "$price<br>";
            } else {
                // Handle the case where the unit price couldn't be fetched
                $priceInfo .= "N/A<br>";
            }

            $productInfo .= "$productName<br>";
            $quantityInfo .= "$quantity<br>";

            // Close the statement for fetching the unit price
            $stmtUnitPrice->close();
        }

        echo "<td>$productInfo</td>";
        echo "<td>$quantityInfo</td>";
        echo "<td>$priceInfo</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>No Transaction found.</p>";
}

    // Close the database connection
    $socket->close();
    ?>


    <?php include 'footer.php'; ?>

    <script>
        // Function to add a new edited sales item input fields
        function addEditedSalesItem() {
            const editedSalesDataDiv = document.getElementById('editedSalesData');
            const newItemDiv = document.createElement('div');
            newItemDiv.className = 'editedSalesItem';
            newItemDiv.innerHTML = `
                <label for="productName">Product Name:</label>
                <input type="text" name="editedSalesData[productName][name]" required>
                <label for="quantity">Quantity:</label>
                <input type="number" name="editedSalesData[productName][quantity]" required>
            `;
            editedSalesDataDiv.appendChild(newItemDiv);
        }
    </script>
</body>
</html>