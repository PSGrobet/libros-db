<?php
// include config file
require_once "config.php";

//Define variables with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = ""; 

// Process form data when submitted
if($_SERVER['REQUEST_METHOD'] == "POST") {

  // Validate username
  if(empty(trim($_POST['username']))) {
    $username_err = "Escribe un nombre de usuario";
  } else {
    // Prepare a SELECT statement / Verificar que no existe ya
    $sql = "SELECT user_id FROM users WHERE username = ?";

    if($stmt = $mysqli->prepare($sql)) {
      // Bind variables as parameters
      $stmt->bind_param("s", $param_username);

      // Set parameters
      $param_username = trim($_POST['username']);

      //Attempt to execute prepared statement / Ejecuta y guarda el resultado / si devuelve resultado es que ya existe usuario
      if($stmt->execute()) {
        //store result
        $stmt->store_result();

        if($stmt->num_rows == 1) {
          $username_err = "Este nombre de usuario ya existe.";
        } else {
          $username = trim($_POST['username']);
        }
      } else {
        echo "Algo salió mal, intenta de nuevo más tarde.";
      }

      // Close statement
      $stmt->close();
    }
  }

  // Validate password
  if(empty(trim($_POST['password']))) {
    $password_err = "Escribe una contraseña de 8 caracteres o más.";
  } elseif (strlen(trim($_POST['password'])) < 8) {
    $password_err = "La contraseña debe ser de 8 caracteres o más.";
  } else {
    $password = trim($_POST['password']);
  }

  // Validate confirm password
  if(empty(trim($_POST['confirm-password']))) {
    $confirm_password_err = "Por favor confirma la contraseña.";
  } else {
    $confirm_password = trim($_POST['confirm-password']);
    if(empty($password_err) && $password != $confirm_password) {
      $confirm_password_err = "la contraseña no coincide";
    }
  }

  // Check input errors before inserting into database
  if(empty($username_err) && empty($password_err) && empty($confirm_password_err)) {

    //Prepare statement
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

    if($stmt = $mysqli->prepare($sql)) {
      // Bind parameters
      $stmt->bind_param("ss", $param_username, $param_password);

      //Set parameters
      $param_username = $username;
      $param_password = password_hash($password, PASSWORD_DEFAULT); // crea un hash de la contraseña

      // Attempt to execute
      if($stmt->execute()) {
        echo "Usuario creado: Correcto <br>";
        //$stmt->close();
      } else {
        echo "Algo salió mal. Intenta más tarde.";
      }

      //Close statement
      $stmt->close();


    } else echo "error";
  header("location: login.php");
  }
  $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./css/styles.css?v=1.2">
  <title>Registro</title>
</head>
<body>
  <div class="page-header">
    <h2 class="titulo">Registra un nombre de usuario y contraseña</h2>
  </div>
  <div class="container-form">
    <form action="register.php" method="post">
      <div class="form-group <? echo (!empty($username_err)) ? 'has-error' : '' ; ?>">
        <label for="username">Nombre de usuario</label>
        <input type="text" name="username" id="username">
        <span class="help-block"><?php echo $username_err ?></span>
      </div>
      <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : '' ; ?>">
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password">
        <span class="help-block"><?php echo $password_err ?></span>
      </div>
      <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : '' ; ?>">
        <label for="conf-pass">Confirma tu contraseña</label>
        <input type="password" name="confirm-password" id="conf-pass">
        <span class="help-block"><?php echo $confirm_password_err ?></span>
      </div>
      <div class="form-group" id="buttons">
        <input type="submit" class="button submit" value="Enviar">
        <input type="reset" class="button reset" value="Borrar">
      </div>
      <p>¿Ya tienes cuenta? <a href="login.php">Entra aquí</a>.<p>
    </form>
  </div>  
</body>
</html>

