<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include './io/databaseHandle.php';

    // Retrieve data from form submission
    $username = $_POST["username"];
    $products = $_POST["product"];

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
        // Initialize arrays to store product names with different errors
        $invalidProducts = [];
        $quantityExceededProducts = [];
        $totalPrice = 0;

        // Calculate the total quantity of products and validate product names and quantities
        $validTransaction = true; // Flag to track if the transaction is valid

        foreach ($products as $key => $product) {
            $productName = $product["name"];
            $quantity = $product["quantity"];
            
            // Check if the product exists in the inventory
            $queryCheckProduct = "SELECT currentAmt, unitPrice FROM mysql_schema.inventory WHERE productName = ?";
            $stmtCheckProduct = $socket->prepare($queryCheckProduct);
            $stmtCheckProduct->bind_param("s", $productName);
            $stmtCheckProduct->execute();
            $stmtCheckProduct->bind_result($currentAmt, $unitPrice);
            
            if ($stmtCheckProduct->fetch()) {
                // Check if the sale quantity is valid (not more than currentAmt)
                if ($quantity <= $currentAmt) {
                    // Product exists in inventory, update the quantity and currentAmt
                    $totalPrice += $quantity * $unitPrice; // Calculate the total price
                    
                    // Close the statement for fetching the result before executing the UPDATE query
                    $stmtCheckProduct->close();
                    
                    // Update the currentAmt in inventory
                    $newCurrentAmt = $currentAmt - $quantity;
                    $queryUpdateInventory = "UPDATE mysql_schema.inventory SET currentAmt = ? WHERE productName = ?";
                    $stmtUpdateInventory = $socket->prepare($queryUpdateInventory);
                    $stmtUpdateInventory->bind_param("ds", $newCurrentAmt, $productName);
                    $stmtUpdateInventory->execute();
                    $stmtUpdateInventory->close();
                } else {
                    // Sale quantity exceeds currentAmt, add it to the list of quantity exceeded products
                    $quantityExceededProducts[] = $productName;
                    // Close the statement for fetching the result
                    $stmtCheckProduct->close();
                    $validTransaction = false; // Mark the transaction as invalid
                }
            } else {
                // Product does not exist in inventory, add it to the list of invalid products
                $invalidProducts[] = $productName;
                // Close the statement for fetching the result
                $stmtCheckProduct->close();
                $validTransaction = false; // Mark the transaction as invalid
            }
        }

        // Check if the transaction is valid before inserting it into the database
        if ($validTransaction) {
            // Calculate the total price based on the products
            $queryCommitstoretransactions = "INSERT INTO mysql_schema.storetransactions (username, stockIDs_serialized, txnSum) VALUES (?, ?, ?)";
            $stmt = $socket->prepare($queryCommitstoretransactions);

            // Serialize an array of products for the 'stockIDs_serialized' column
            $serializedProducts = serialize($products);

            // Bind the username, serialized products, and total price as a float(10,2)
            $stmt->bind_param("ssd", $username, $serializedProducts, $totalPrice);

            $queryAttempt = $stmt->execute();

            if (!$queryAttempt) {
                echo '<script>console.log("<debug> Transaction commit failed"); </script>';
            } else {
                echo '<script>console.log("<debug> Transaction commit succeeded"); </script>';
            }
            $stmt->close();
        } else {
            // Display error messages for invalid products and quantity exceeded products
            if (!empty($invalidProducts)) {
                echo '<div style="color: red;">The following products are invalid and do not exist in inventory: ' . implode(", ", $invalidProducts) . '</div>';
            }
            if (!empty($quantityExceededProducts)) {
                echo '<div style="color: red;">The following products have quantities that exceed current inventory: ' . implode(", ", $quantityExceededProducts) . '</div>';
            }
        }
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
        <button type="button" onclick="deleteProduct(this)">Delete</button>
    </div>
</div>
<button type="button" onclick="addProduct()">Add Product</button>
<input type="submit" value="Add">

    </form>
    <?php
    include './io/databaseHandle.php';

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
            <button type="button" onclick="deleteProduct(this)">Delete</button>
        `;
        
        productsDiv.appendChild(newProductDiv);
    }

    // Function to delete a product input field
    function deleteProduct(button) {
        const productDiv = button.parentElement;
        const productsDiv = document.getElementById('products');
        productsDiv.removeChild(productDiv);
    }
</script>
</body>
</html>
