<?php
////INCLUDE THIS NEXT LINE TO LOCK A PAGE////
include './verif/content-restrict.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include './io/databaseHandle.php';

    // Retrieve data from form submission
    $txnID = $_POST["txnID"];
    $products = $_POST["product"];

    $query= "SELECT * FROM mysql_schema.storetransactions WHERE transactionID = '$txnID'";
    $txnCheck= $socket->execute_query($query, null);

    if (!$txnCheck) {
      echo '<script>console.log("<debug> Database cannot be contacted!")</script>';
    } else {
      //assuming a MYSQLI_RESULT is fetched, get only the first result of the array
      //as U is enforced on the end of the database theres no need for advanced array seeking
        
        // query item database for unit price
        //get unit amount and sum total
        foreach ($products as $prodName=> $purchaseAmount) {
            echo '<script>console.log("<debug>'.$products.'"); </script>';
            
        }
        //commit with sum total 

      $updatedProductTxn= serialize($products); 

      $query= "UPDATE mysql_schema.storetransactions SET stockIDs_serialized= '$updatedProductTxn' WHERE transactionID= '$txnID'";
      $queryAttempt = $socket->execute_query($query, null);
      
      if (!$queryAttempt) {
        echo '<script>console.log("<debug> Product modification failed"); </script>';
      } else {
        echo '<script>console.log("<debug> Product modified successfully"); </script>';
      }
    }


    $socket->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="store_transaction">
    <title>Edit Store Transaction</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'menu.php'; ?>
    <h1>Edit Sales Record</h1>



    <form action="" method="POST">
        <label for="txnID">Transaction ID:</label>
        <input type="text" name="txnID" id="txnID" required>

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
      <button type="button" onclick="addProduct()">Add another product</button>
      <input type="submit" value="Submit modification">

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


    // search tbody for txnID- saves on a database query
    // as table elements dod not use divID, xpath 
    function txnIDsearch(){

    }
</script>
</body>
</html>
