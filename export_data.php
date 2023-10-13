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
include './io/databaseHandle.php';
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedTables = [];
    //choosing tables
    if(isset($_POST['people'])) $selectedTables[] = 'people';
    if(isset($_POST['inventory'])) $selectedTables[] = 'inventory';
    if(isset($_POST['storetransactions'])) $selectedTables[] = 'storetransactions';


    //csv constants, field descriptors in DB
    $exportDefs= array(
        array('UUID', 'staffType', 'username', 'surname', 'givenName', 'pwdHash', 'userEmail'),     //USER DATA
        array('stockID', 'productName', 'currentAmt', 'unitPrice'),                                 //STOCK DATA
        array('transactionID', 'username', 'stockIDs_serialized', 'txnSum', 'timestamp', 'desc')    //TXN DATA
    );

    ///HEADER DATA///
    $serverTime= $_SERVER['REQUEST_TIME'];
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="exports-'.$serverTime.'.csv"');


    ///OPEN WRITEFILE HANDLE, DIRECTED TO OUTPUT BUFFER///
    $buffer = fopen('php://output', 'w');
    $buffer.ob_clean();
    
    //iterate through selected tables
    foreach ($selectedTables as $tableName) {


        ///QUERY OVERHEAD///
        $query = "SELECT * FROM mysql_schema.$tableName";
        $result = $socket->execute_query($query, null);

        //column naming for each csv
        switch($tableName) {
            case 'people':
                fputcsv($buffer, $exportDefs[0]);
                break;
            case 'inventory':
                fputcsv($buffer, $exportDefs[1]);
                break;
            case 'storetransactions':
                fputcsv($buffer, $exportDefs[2]);
                break;
            }
            
            while ($row = mysqli_fetch_assoc($result)) {
                fputcsv($buffer, $row);
            }
        }
        

    //close the output buffer
    fclose($buffer);

    //closing connection socket
    $socket->close();
    exit;
}
?>
    <h1>Export Data</h1>
    <form action="" method="post">
        <label><input type="checkbox" name="people">Export People Data</label><br>
        <label><input type="checkbox" name="inventory">Export Inventory Data</label><br>
        <label><input type="checkbox" name="storetransactions">Export Transaction Data</label><br><br>
        <input type="submit" value="Export Selected Data">
    </form>
</body>
</html>
