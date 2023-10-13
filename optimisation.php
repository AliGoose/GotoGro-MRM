<?php
include 'io/databaseHandle.php'; // Include your database connection script

// Weekly transactions data
$sql_week = "SELECT SUM(txnSum) as total, DAYNAME(timestamp) as dayOfWeek FROM storetransactions GROUP BY DAYOFWEEK(timestamp), DAYNAME(timestamp)";
$result_week = $socket->query($sql_week);
$dataPointsWeek = array();
while ($row = $result_week->fetch_assoc()) {
    $dataPointsWeek[] = array("y" => $row['total'], "label" => $row['dayOfWeek']);
}

// Monthly transactions data
$sql_month = "SELECT SUM(txnSum) as total, MONTHNAME(timestamp) as monthName FROM storetransactions GROUP BY MONTH(timestamp), MONTHNAME(timestamp)";
$result_month = $socket->query($sql_month);
$dataPointsMonth = array();
while ($row = $result_month->fetch_assoc()) {
    $dataPointsMonth[] = array("y" => $row['total'], "label" => $row['monthName']);
}

// Inventory data
$sql_inventory = "SELECT productName, currentAmt FROM inventory";
$result_inventory = $socket->query($sql_inventory);
$dataPointsInventory = array();
while ($row = $result_inventory->fetch_assoc()) {
    $dataPointsInventory[] = array("y" => $row['currentAmt'], "label" => $row['productName']);
}

$socket->close();
?>

<!DOCTYPE HTML>
<html>
<head>
<script>
function renderChart(dataPoints, titleText, axisYTitle) {
    var chart = new CanvasJS.Chart("chartContainer", {
        title: {
            text: titleText
        },
        axisY: {
            title: axisYTitle
        },
        data: [{
            type: "line",
            dataPoints: dataPoints
        }]
    });
    chart.render();
}

window.onload = function () {
    renderChart(<?php echo json_encode($dataPointsWeek, JSON_NUMERIC_CHECK); ?>, "Transaction Sum Over a Week", "Sum of Transactions");
}

function displayWeek() {
    renderChart(<?php echo json_encode($dataPointsWeek, JSON_NUMERIC_CHECK); ?>, "Transaction Sum Over a Week", "Sum of Transactions");
}

function displayMonth() {
    renderChart(<?php echo json_encode($dataPointsMonth, JSON_NUMERIC_CHECK); ?>, "Transaction Sum Over a Month", "Sum of Transactions");
}

function displayInventory() {
    var chart = new CanvasJS.Chart("chartContainer", {
        title: {
            text: "Current Inventory Stock"
        },
        axisY: {
            title: "Amount"
        },
        data: [{
            type: "bar",
            dataPoints: <?php echo json_encode($dataPointsInventory, JSON_NUMERIC_CHECK); ?>
        }]
    });
    chart.render();
}
</script>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'menu.php'; ?>

<button onclick="displayWeek()">Display Weekly Transactions</button>
<button onclick="displayMonth()">Display Monthly Transactions</button>
<button onclick="displayInventory()">Display Inventory</button>

<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <?php include 'footer.php'; ?>
</body>
</html>
