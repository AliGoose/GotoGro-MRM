<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member Details</title>
</head>
<body>

<?php
// need databse sql here

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["member_id"])) {
    $member_id = $_GET["member_id"];

    $sql = "SELECT * FROM members WHERE id = $member_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $firstName = $row["first_name"];
        $lastName = $row["last_name"];
        $mobileNumber = $row["mobile_number"];
        $address = $row["address"];
        $email = $row["email"];
    } else {
        echo "Member not found.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST["member_id"];
    $firstName = $_POST["first_name"];
    $lastName = $_POST["last_name"];
    $mobileNumber = $_POST["mobile_number"];
    $address = $_POST["address"];
    $email = $_POST["email"];


    $sql = "UPDATE members SET first_name='$firstName', last_name='$lastName', email='$email', mobile_Number='$mobileNumber', address='address' WHERE id = $member_id";

    if ($conn->query($sql) === TRUE) {
        echo "Member details updated successfully.";
    } else {
        echo "Error updating member details: " . $conn->error;
    }
}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
    FirstName: <input type="text" name="first_name" value="<?php echo $lastName; ?>"><br>
    LastName: <input type="text" name="last_name" value="<?php echo $firstName; ?>"><br>
    Email: <input type="text" name="email" value="<?php echo $email; ?>"><br>
    Mobile: <input type="number" name="mobile_number" value="<?php echo $mobileNumber; ?>"><br>
    Address: <input type="text" name = "address" value ="<?php echo $address; ?>"><br>
    <input type="submit" value="Update Details">
</form>

</body>
</html>
