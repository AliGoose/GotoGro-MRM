<!DOCTYPE html>
<html lang="en">
  <?php session_start();?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Message</title>
</head>
<body>
  <p>
    <?php 
      echo $_SESSION["message"];
      header("refresh: 4, url={$_GET['redirect']}")
    ?>
    <br>You will be redirected in 4 seconds.

  </p>
</body>
</html>