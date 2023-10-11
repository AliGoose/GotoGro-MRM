<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Data</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php
    include 'header.php';
    include 'menu.php';
    include './io/databaseHandle.php';
    ?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedTables = [];
    //choosing tables
    if(isset($_POST['people'])) $selectedTables[] = 'people';
    if(isset($_POST['inventory'])) $selectedTables[] = 'inventory';
    if(isset($_POST['storetransactions'])) $selectedTables[] = 'storetransactions';

    foreach ($selectedTables as $tableName) {
        $query = "SELECT * FROM mysql_schema.$tableName";
        $result = $socket->execute_query($query, null);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="exported_'.$tableName.'.csv"');

        $output = fopen('php://output', 'w');
        //column names for each field in csv
        switch($tableName) {
            case 'people':
                fputcsv($output, array('UUID', 'staffType', 'username', 'surname', 'givenName', 'pwdHash', 'userEmail'));
                break;
            case 'inventory':
                fputcsv($output, array('stockID', 'productName', 'currentAmt', 'unitPrice'));
                break;
            case 'storetransactions':
                fputcsv($output, array('transactionID', 'username', 'stockIDs_serialized', 'txnSum', 'timestamp'));
                break;
        }

        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, $row);
        }
        fclose($output);
    }
    //closing the exportation from including more data
    $socket->close();
    exit;
}
?>
    <h1>Export Data</h1>
    <form action="" method="post">
        <label><input type="checkbox" name="people">Click here to Export People Data</label><br>
        <label><input type="checkbox" name="inventory">Click here to Export Inventory Data</label><br>
        <label><input type="checkbox" name="storetransactions">Click here to Export Store Transactions Data</label><br><br>
        <input type="submit" value="Export Selected Data">
    </form>
</body>
</html>
