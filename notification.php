<?php
include 'io/databaseHandle.php'; // Include your database connection script

// Retrieve and display unread notifications
$sql = "SELECT * FROM notifications WHERE readStatus = 0 ORDER BY issued DESC";
$result = $socket->query($sql);

if ($result) {
    echo "<h1>Unread Notifications</h1>";
    echo "<ul>";
    while ($notification = $result->fetch_assoc()) {
        echo "<li>";
        echo "<strong>{$notification['subject']}</strong><br>";
        echo "{$notification['content']} - {$notification['issued']}<br>";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No unread notifications found.</p>";
}

$socket->close();
?>
