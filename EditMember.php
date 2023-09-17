<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member Details</title>
</head>
<body>

<?php
// Assuming you have a database connection established

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["member_id"])) {
    $member_id = $_GET["member_id"];

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST["member_id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];

    $sql = "UPDATE members SET name='$name', email='$email', phone='$phone' WHERE id = $member_id";

    if ($conn->query($sql) === TRUE) {
        echo "Member details updated successfully.";
    } else {
        echo "Error updating member details: " . $conn->error;
    }
}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
    Name: <input type="text" name="name" value="<?php echo $name; ?>"><br>
    Email: <input type="text" name="email" value="<?php echo $email; ?>"><br>
    Phone: <input type="text" name="phone" value="<?php echo $phone; ?>"><br>
    <input type="submit" value="Update Details">
</form>

</body>
</html>
