<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Member</title>
</head>
<body>

<?php
//need database sql query here

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["member_id"])) {
    $member_id = $_GET["member_id"];

    // Display member details before removal
    $sql = "SELECT * FROM members WHERE id = $member_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $name = $row["name"];
        $email = $row["email"];
        $phone = $row["phone"];
    } else {
        echo "Member not found.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirm"])) {
    $member_id = $_POST["member_id"];

    // Perform the removal
    $sql = "DELETE FROM members WHERE id = $member_id";

    if ($conn->query($sql) === TRUE) {
        echo "Member removed successfully.";
    } else {
        echo "Error removing member: " . $conn->error;
    }
}
?>

<?php if (isset($name) && isset($email) && isset($phone)) { ?>
    <h2>Confirm Removal of Member</h2>
    <p>Name: <?php echo $name; ?></p>
    <p>Email: <?php echo $email; ?></p>
    <p>Phone: <?php echo $phone; ?></p>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
        <input type="submit" name="confirm" value="Confirm Removal">
    </form>
<?php } ?>

</body>
</html>
