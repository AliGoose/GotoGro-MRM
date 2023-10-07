<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include './io/databaseHandle.php';

    // Retrieve data from form submission
    $username = $_POST["username"];

    // Check if the username exists in the 'people' table
    $queryCheckUsername = "SELECT COUNT(*) FROM mysql_schema.people WHERE username = ?";
    
    $stmtCheckUsername = $socket->prepare($queryCheckUsername);
    $stmtCheckUsername->bind_param("s", $username);
    $stmtCheckUsername->execute();
    $stmtCheckUsername->bind_result($userExists);
    $stmtCheckUsername->fetch();
    $stmtCheckUsername->close();

    if ($userExists) {
        // Username exists, proceed with the transaction
        // Calculate the total quantity of products
        $totalQuantity = 0;

        foreach ($_POST["product"] as $key => $product) {
            $quantity = $product["quantity"];
            $totalQuantity += $quantity;
        }

        // Assuming transactionID is an auto-increment column, you don't need to specify it
        $queryCommitstoretransactions = "INSERT INTO mysql_schema.storetransactions VALUES (DEFAULT, ?, ?, ?, DEFAULT)";
        $stmt = $socket->prepare($queryCommitstoretransactions);

        // Bind the username, products
        $stmt->bind_param("ssd", $username, $username, $totalQuantity);


        $queryAttempt = $stmt->execute();

        if (!$queryAttempt) {
            echo '<script>console.log("<debug> Transaction commit failed"); </script>';
        } else {
            echo '<script>console.log("<debug> Transaction commit succeeded"); </script>';
        }
        $stmt->close();
    } else {
        // Username does not exist, display an error message on the page
        echo '<div style="color: red;">Username does not exist. Please check the username.</div>';
    }

    $socket->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="store_transaction">
    <title>Store Transaction</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'menu.php'; ?>
    <h1>Add Sales Record</h1>
    <form action="" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <!-- Product input fields (can be dynamically added) -->
        <div id="products">
            <div class="product">
                <label for="product[0][name]">Product Name:</label>
                <input type="text" name="product[0][name]" required>
                <label for="product[0][quantity]">Quantity:</label>
                <input type="number" name="product[0][quantity]" required>
            </div>
        </div>
        <button type="button" onclick="addProduct()">Add Product</button>
        <input type="submit" value="Add">
    </form>
    <?php include 'footer.php'; ?>

    <script>
        // Function to add a new product input field
        function addProduct() {
            const productsDiv = document.getElementById('products');
            const productCount = productsDiv.querySelectorAll('.product').length;
            
            const newProductDiv = document.createElement('div');
            newProductDiv.className = 'product';
            newProductDiv.innerHTML = `
                <label for="product[${productCount}][name]">Product Name:</label>
                <input type="text" name="product[${productCount}][name]" required>
                <label for="product[${productCount}][quantity]">Quantity:</label>
                <input type="number" name="product[${productCount}][quantity]" required>
            `;
            
            productsDiv.appendChild(newProductDiv);
        }
    </script>
</body>
</html>
