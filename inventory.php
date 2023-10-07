<?php

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
	<?php include 'footer.php'; ?>
</body>
</html>
