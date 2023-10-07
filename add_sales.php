<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include './io/databaseHandle.php';

    // Retrieve data from form submission
    $username = $_POST["username"];
    $price = $_POST["price"];


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
        // Serialize the PHP array (stockIDs) to store in the BLOB column
        $stockIDs = []; // Replace this with your array data
        $stockIDs_serialized = serialize($stockIDs);

        // Assuming transactionID is an auto-increment column, you don't need to specify it
        $queryCommitstoretransactions = "INSERT INTO mysql_schema.storetransactions VALUES (DEFAULT, ?, ?, '$price', DEFAULT)";
        $stmt = $socket->prepare($queryCommitstoretransactions);

        // Bind the username and serialized data
        $stmt->bind_param("ss", $username, $stockIDs_serialized);

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
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="price">Price per Unit:</label>
        <input type="number" name="price" id="price" required>
        <input type="submit" value="Store Transaction">
    </form>
    <?php include 'footer.php'; ?>
</body>
</html>
