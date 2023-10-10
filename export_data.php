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
    include './io/databaseHandle.php'; // Include the database handle
    ?>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check which tables were selected
        $selectedTables = [];
        if(isset($_POST['people'])) $selectedTables[] = 'people';
        if(isset($_POST['inventory'])) $selectedTables[] = 'inventory';
        if(isset($_POST['storetransactions'])) $selectedTables[] = 'storetransactions';

        //creating csv files
        foreach ($selectedTables as $tableName) {
            $query = "SELECT * FROM mysql_schema.$tableName";
            $result = $socket->execute_query($query, null);

            
            $output = fopen("exported_$tableName.csv", 'w');

            //adding the tables to csv with columns
            fputcsv($output, array('Column1', 'Column2', 'Column3')); // Adjust column names

            //adding the tables to csv with rows
            while ($row = mysqli_fetch_assoc($result)) {
                fputcsv($output, $row);
            }
            fclose($output);
        }

        // Offer files for download
        $zip = new ZipArchive();
        $zipFileName = 'exported_data.zip';
        if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
            foreach ($selectedTables as $tableName) {
                $csvFileName = "exported_$tableName.csv";
                $zip->addFile($csvFileName, $csvFileName);
            }
            $zip->close();
        }

        // Clean up temporary CSV files
        foreach ($selectedTables as $tableName) {
            unlink("exported_$tableName.csv");
        }

        // Offer the ZIP file for download
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="exported_data.zip"');
        readfile($zipFileName);

        // Clean up temporary ZIP file
        unlink($zipFileName);

        $socket->close();
        exit;
    }
    ?>

    <h1>Export Data</h1>

    <form action="" method="post">
        <label><input type="checkbox" name="people"> Export People Data</label><br>
        <label><input type="checkbox" name="inventory"> Export Inventory Data</label><br>
        <label><input type="checkbox" name="storetransactions"> Export Store Transactions Data</label><br><br>
        <input type="submit" value="Export Selected Data">
    </form>
</body>
</html>
