<?php
// Initialize session
session_start();

// Check if user is logged in, or redirect to login page
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header("location: login.php");
  exit;
}

// Include config file
require_once "config.php";

// Define variables, initialize with empty values

$old_password = $new_password = $confirm_password = "";
$old_password_err = $new_password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if($_SERVER['REQUEST_METHOD'] == "POST") {

  // Validate old password
  if(empty(trim($_POST['old_password']))) {
    $old_password_err = "Por favor escribe tu contraseña actual";
  } else {
    $old_password = trim($_POST['old_password']);

    // Prepare a statement
    $sql = "SELECT password FROM users WHERE user_id = ?;";

    if($stmt = $mysqli->prepare($sql)) {  
      // Bind variables
      $stmt->bind_param("i", $param_id);

      // Set parameter
      $param_id = $_SESSION['id'];

      // Attempt to execute
      if($stmt->execute()) {
        // Store result
        $stmt->store_result();

        if($stmt->num_rows == 1) {
          //Bind result variables
          $stmt->bind_result($hashed_password);
          if($stmt->fetch()) {
            if(!password_verify($old_password, $hashed_password)) {
              $old_password_err = "La contraseña es incorrecta.";
            }
          }
        }
      } else {
        echo "Algo salió mal, intenta más tarde.";
      }
      $stmt->close();
    } else {
      echo "Algo salió mal.";
    }
  }


  // Validate new password
  if(empty(trim($_POST['new_password']))) {
    $new_password_err = "Por favor escribe una nueva contraseña";
  } elseif(strlen(trim($_POST['new_password'])) < 8) {
    $new_password_err = "La contraseña debe tener al menos 8 caracteres";
  } else {
    $new_password = trim($_POST['new_password']);
  }

  // Validate confirm password
  if(empty(trim($_POST['new_password']))) {
    $confirm_password_err = "Por favor confirma la contraseña";
  } else {
    $confirm_password = trim($_POST["confirm_password"]);
    if(empty($new_password_err) && ($new_password != $confirm_password)) {
      $confirm_password_err = "La contraseña no coincide.";
    }
  }

  // Check input error before updating
  if(empty($old_password_err) && empty($new_password_err) && empty($confirm_password_err)) {
    // Prepare a statement
    $sql = "UPDATE users SET password = ? WHERE user_id = ?";

    if($stmt = $mysqli->prepare($sql)) {
      // bind variables
      $stmt->bind_param("si", $param_password, $param_id);

      // Set parameters
      $param_password = password_hash($new_password, PASSWORD_DEFAULT);
      $param_id = $_SESSION['id'];

      // Attempt to execute
      if($stmt->execute()) {
        // Password updated succesfully, destroy session and redirect to login
        session_destroy();
        header("location: login.php");
        exit();
        
      } else {
        echo "Algo salió mal, intenta más tarde.";
      }

      // Close statement
      $stmt->close();
    }
  }

  // Close connection
  $mysqli->close();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="./css/styles.css?v=1.4">
  <title>Login</title>
</head>
<body>
  <div class="page-header">
    <h2 class="titulo">Cambia tu contraseña</h2>
  </div>
  <div class="container-form">
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
      <div class="form-group <?php echo (!empty($old_password_err)) ? 'has-error' : '' ; ?>">
        <label for="old_password">Tu contraseña actual</label>
        <input type="password" name="old_password" id="old_password">
        <span class="help-block"><?php echo $old_password_err ?></span>
      </div>
      <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : '' ; ?>">
        <label for="new_password">Nueva contraseña</label>
        <input type="password" name="new_password" id="new_password">
        <span class="help-block"><?php echo $new_password_err ?></span>
      </div>
      <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : '' ; ?>">
        <label for="confirm_password">Confirma nueva contraseña</label>
        <input type="password" name="confirm_password" id="confirm_password">
        <span class="help-block"><?php echo $confirm_password_err ?></span>
      </div>
      <div class="form-group" id="buttons">
        <input type="submit" class="button submit" value="Cambiar">
        <input type="reset" class="button reset" value="Borrar">
      </div>
      
    </form>
  </div>

</body>
</html>