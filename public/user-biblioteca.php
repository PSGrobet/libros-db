<?php
// Initioalize session
session_start();

// Check if user is logged in, if not, send to login
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header("location: login.php");
  exit();
}

require_once "config.php";

$mensaje_admin = "";




?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="./css/styles.css?v=1.3">
  <title>Mi biblioteca</title>
</head>
<body>

  <div class="page-header">
    <h2 class="titulo">Tu biblioteca</h2>
  </div>
  <h3 class="mensaje-admin"><php echo $mensaje_admin;?></h3>
  
</body>
</html>