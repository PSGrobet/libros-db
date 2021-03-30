<?php
// Initialize session
session_start();

// If user is logged in redirect to user page
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  header("location: user.php");
  exit;
  
}

// Include config file
require_once "config.php";

// Define variables with empty values
$username = $password = "";
$username_err = $password_err = "";

// Process data if submitted
if($_SERVER['REQUEST_METHOD'] == "POST"){

  // Check if username is empty
  if(empty(trim($_POST['username']))) {
    $username_err = "Escribe tu nombre de usuario.";
  } else {
    $username = trim($_POST['username']);
  }

  // Check if password is empty
  if(empty(trim($_POST['password']))) {
    $password_err = "Escribe tu contraseña.";
  } else {
    $password = trim($_POST['password']);
  }

  // Validate credentials
  if(empty($username_err) && empty($password_err)) {
    // Prepare a statement
    $sql = "SELECT user_id, username, password FROM users WHERE username = ?";

    if($stmt = $mysqli->prepare($sql)) {
      // Bind variables as parameters
      $stmt->bind_param("s", $param_username);

      // Set parameters
      $param_username = $username;

      // Attempt to execute
      if($stmt->execute()) {
        // Store result
        $stmt->store_result();

        // Chack if username exists, if yes, verify password
        if($stmt->num_rows == 1) {
          // Bind result variables
          $stmt->bind_result($id, $username, $hashed_password);
          if($stmt->fetch()) { // "Fetch results from a prepared statement into the bound variables" https://www.php.net/manual/en/mysqli-stmt.fetch.php
            if(password_verify($password, $hashed_password)) {
              // Password correct, start new session
              session_start();

              // Store data in session variables
              $_SESSION['loggedin'] = true;
              $_SESSION['id'] = $id;
              $_SESSION['username'] = $username;

              // Redirect to user page
              header("location: user.php");
            } else {
              // Password not valid
              $password_err = "La contraseña es incorrecta.";
            }
          }
        } else {
          // Username not found
          $username_err = "No se encontró una cuenta con ese nombre de usuario.";
        }
      } else {
        // error en el 'execute'
        echo "Algo salió mal. Por favor intenta más tarde.";
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
<html lang="en">
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
    <h2 class="titulo">Ingresa a tu cuenta</h2>
  </div>
  <div class="container-form">
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
      <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : '' ; ?>">
        <label for="username">Nombre de usuario</label>
        <input type="text" name="username" id="username">
        <span class="help-block"><?php echo $username_err ?></span>
      </div>
      <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : '' ; ?>">
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password">
        <span class="help-block"><?php echo $password_err ?></span>
      </div>
      <div class="form-group" id="buttons">
        <input type="submit" class="button submit" value="Entrar">
        <input type="reset" class="button reset" value="Borrar">
      </div>
      <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>.</p>
    </form>
  </div>

</body>
</html>


