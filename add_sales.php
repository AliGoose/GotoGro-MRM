<?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        include './io/databaseHandle.php';
        
        // Retrieve data from form submission
        $product_name = $_POST["product_name"];
        $quantity = $_POST["quantity"];
        $price = $_POST["price"];
             
        $queryCommitInventory = "insert into mysql_schema.inventory values (DEFAULT, '$product_name', '$quantity', '$price', DEFAULT)";
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
    <title>Add Sales Record</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
	<?php include 'header.php'; ?>
    <?php include 'menu.php'; ?>
    <h1>Add Sales Record</h1>
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
        <input type="submit" value="Add Sale Record">
    </form>
	<?php include 'footer.php'; ?>
</body>
</html>
