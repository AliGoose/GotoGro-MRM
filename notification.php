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
    <?php
    include 'io/databaseHandle.php'; // Include your database connection script

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['mark_as_read']) && !empty($_POST['notification_ids'])) {
            $ids = implode(',', array_map('intval', $_POST['notification_ids']));
            $sql_update = "UPDATE notifications SET readStatus = 1 WHERE notificationID IN ($ids)";
            $socket->query($sql_update);
        } elseif (isset($_POST['mark_as_unread']) && !empty($_POST['notification_ids'])) {
            $ids = implode(',', array_map('intval', $_POST['notification_ids']));
            $sql_update = "UPDATE notifications SET readStatus = 0 WHERE notificationID IN ($ids)";
            $socket->query($sql_update);
        }
    }

    $sql_count = "SELECT COUNT(*) AS unread_count FROM notifications WHERE readStatus = 0";
    $result_count = $socket->query($sql_count);
    $row_count = $result_count->fetch_assoc();
    $unread_count = $row_count['unread_count'];

    echo "<button>Notifications ($unread_count)</button>";

    $sql = "SELECT * FROM notifications WHERE readStatus = 0 ORDER BY issued DESC";
    $result = $socket->query($sql);

    if ($result && $result->num_rows > 0) {
        echo "<h1>Unread Notifications</h1>";
        echo "<form method='post'>";
        echo "<ul>";
        while ($notification = $result->fetch_assoc()) {
            echo "<li>";
            echo "<input type='checkbox' name='notification_ids[]' value='{$notification['notificationID']}'> ";
            echo "<strong>{$notification['subject']}</strong><br>";
            echo "{$notification['content']} - {$notification['issued']}<br>";
            echo "</li>";
        }
        echo "</ul>";
        echo "<input type='submit' name='mark_as_read' value='Mark As Read'>";
        echo "<input type='submit' name='mark_as_unread' value='Mark As Unread'>";
        echo "</form>";
    } else {
        echo "<p>No unread notifications found.</p>";
    }

    $socket->close();
    ?>
    <?php include 'footer.php'; ?>
</body>
</html>
