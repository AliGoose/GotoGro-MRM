<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Receipt</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    
    <?php
    include 'header.php';
    include './io/databaseHandle.php';
    include './cpdf/tcpdf.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $transactionID = $_POST["transactionID"];

        $query = "SELECT username, transactionID, txnSum FROM mysql_schema.storetransactions WHERE transactionID = '$transactionID'";
        $result = $socket->execute_query($query, null);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $username = $row['username'];
            $transactionID = $row['transactionID'];
            $txnSum = $row['txnSum'];

            // Start output buffering
            ob_start();

            require_once("tcpdf");
            require_once('tcpdf/tcpdf.php');
            $pdf = new TCPDF();
            $pdf->SetMargins(10, 10, 10);
            $pdf->AddPage();
            $pdf->SetFont('helvetica', '', 12);

            $content = "
            <h1>Receipt</h1>
            <p><strong>Username:</strong> $username</p>
            <p><strong>Transaction ID:</strong> $transactionID</p>
            <p><strong>Transaction Sum:</strong> $txnSum</p>
            ";
            $pdf->writeHTML($content, true, false, true, false, '');

            // End output buffering and get contents
            $pdf_content = ob_get_clean();

            // Send PDF to browser
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="receipt.pdf"');
            echo $pdf_content;
        } else {
            echo "No records found for the provided Transaction ID.";
        }

        $socket->close();
    }
    ?>

    <h1>Generate Receipt</h1>

    <form action="" method="post">
        <label for="transactionID">Transaction ID:</label>
        <input type="text" name="transactionID" id="transactionID" required><br>

        <input type="submit" value="Generate Receipt">
    </form>

    <?php include 'footer.php'; ?>
</body>
</html>
