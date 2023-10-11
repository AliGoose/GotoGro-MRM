<?php
////INCLUDE THIS NEXT LINE TO LOCK A PAGE////
include './verif/content-restrict.php';


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        include './io/databaseHandle.php';
        
        // Retrieve data from form submission
        $product_name = $_POST["product_name"];
        $quantity = $_POST["quantity"];
        $price = $_POST["price"];
        $picture = $_POST["picture"];
             
        $queryCommitInventory = "insert into mysql_schema.inventory values (DEFAULT, '$product_name', '$quantity', '$price', '$picture')";
        $queryAttempt= $socket->execute_query($queryCommitInventory, null);

        if (!$queryAttempt){
            echo '<script>console.log("<debug> User commit failed"); </script>';
        } else {
            echo '<script>console.log("<debug> User commit succeeded"); </script>';
        }

        $socket->close();
        }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="add_sales_record">
    <title>Inventory</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
	<?php include 'header.php'; ?>
    <?php include 'menu.php'; ?>
    <h1>Inventory</h1>
    <form action="" method="POST">
        <label for="product_name">Product Name:</label>
        <input type="text" name="product_name" id="product_name" required>
        <br>
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" required>
        <br>
        <label for="price">Price per Unit:</label>
        <input type="number" name="price" id="price" required>
        <br>
        <label for="picture">Upload Picture (optional):</label>
        <input type="file" name="picture" id="picture" accept="image/*">
        <br>
        <input type="submit" value="Add Sale Record">
    </form>
    <?php
    include './io/databaseHandle.php';

    // Retrieve and display inventory details
    $querySelectInventory = "SELECT * FROM mysql_schema.inventory";
    $result = $socket->query($querySelectInventory);

    if ($result) {
        echo "<h2>Inventory Details:</h2>";
        echo "<table>";
        echo "<tr><th>Stock ID</th><th>Product Name</th><th>Quantity</th><th>Price per Unit</th><th>Picture</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['stockID']}</td>";
            echo "<td>{$row['productName']}</td>";
            echo "<td>{$row['currentAmt']}</td>";
            echo "<td>{$row['unitPrice']}</td>";
            echo "<td><img src='{$row['productImage']}' alt='Product Picture' width='100'></td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No inventory items found.</p>";
    }

    // Close the database connection
    $socket->close();
    ?>
	<?php include 'footer.php'; ?>
</body>
</html>
