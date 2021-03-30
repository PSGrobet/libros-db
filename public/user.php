<?php
// Initialize session
session_start();

// Check if user is logged in, if not, send to login page
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true ) {
  header("location: login.php");
  exit;
}

require_once "config.php";


  // Mostrar tabla con los libros registrados

// set users book table
$username = $_SESSION['username'];
$user_table = "libros_".$username;

// Obtén user_id
$sql = "SELECT user_id FROM users WHERE username = '$username';";
if($result = $mysqli->query($sql)) {
  $row = $result->fetch_array();
  $user_id = $row['user_id'];
  
}


$message = "";

// Show books in table
$sql = "SELECT * FROM books_main WHERE user_id = $user_id;";

// $stmt = $mysqli->prepare($sql);
// $stmt->bind_param("s", $user_table);

if($result = $mysqli->query($sql)) {
  $books_count = 0;
  if($result->num_rows < 1) {
    $message = "Parece que aún no hay libros en tu biblioteca, comienza agregando algunos.";
    
  } else {
    $books_count = $result->num_rows;
    $table = "<table class='books-table'>
                <tr>
                  <th>ISBN</th>
                  <th>Autor</th>
                  <th>Titulo</th>
                  <th>Categoría</th>
                  <th>Año</th>
                </tr>";

    while($row = $result->fetch_array()) {
      $new_row = "<tr>"
                  ."<td>".$row['isbn']."</td>"
                  ."<td>".$row['autor']."</td>"
                  ."<td>".$row['titulo']."</td>"
                  ."<td>".$row['categoria']."</td>"
                  ."<td>".$row['año']."</td>"
                  ."</tr>";
      $table .= $new_row;
    }
    $table .= "</table>";
  }
} else {
  $message = "Error en execute.";
}

  // Añadir libros //
$input_autor_err = "";
$input_titulo_err = "";

if($_SERVER['REQUEST_METHOD'] == "POST") {
  // variables

  if(isset($_POST['isbn'])) {
    $isbn = trim($_POST['isbn']);
  }
  if(!isset($_POST['autor'])) {
    $input_autor_err = "Escribe el nombre del autor.";
  } else {
    $autor = trim(htmlspecialchars($_POST['autor']));
  }
  if(!isset($_POST['titulo'])) {
    $input_titulo_err = "Escribe el título del libro";
  } else {
    $titulo = trim(htmlspecialchars($_POST['titulo']));
  }
  if(isset($_POST['categoria'])) {
    $categoria = trim(htmlspecialchars($_POST['categoria']));
  }
  if(isset($_POST['año'])) {
    $año = trim(htmlspecialchars($_POST['año']));
  }

  // Prepara statement
  if(empty($input_autor_err) && empty($input_titulo_err)) {
    $sql = "INSERT INTO books_main(book_id, isbn, autor, titulo, categoria, año, user_id) VALUES(?, ?, ?, ?, ?, ?, ?);";
    if($stmt = $mysqli->prepare($sql)) {
      // bind parameters
      $stmt->bind_param("iisssii", $book_id, $isbn, $autor, $titulo, $categoria, $año, $user_id);
      
      //Execute
      if($stmt->execute()) {
        echo "Correcto";
        // vuelve cargar la página
        header("location: user.php");
        $stmt->close();
      } else echo "error 'execute()'.";
    } else echo "Error 'prepare()'.";
    $mysqli->close();
  } else echo "Error input.";
 
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

  <link rel="stylesheet" href="./css/styles.css?v=1.5">
  <link rel="stylesheet" href="./css/styles-user.css?v=1.3">
  <title>Usuario</title>
</head>
<body>
  <div class="main">
    <div class="page-header">
      <h2 class="titulo">Bienvenido <?php echo htmlspecialchars($_SESSION['username']);?></h2>
    </div>
    <div class="acciones">
      <!-- <a class="boton ver" href="user-biblioteca.php">Aministrar mi biblioteca</a> -->
      <a class="boton psw-update" href="psw-update.php">Cambiar contraseña</a>
      <a class="boton salir" href="logout.php">Salir</a>
    </div>
    <p style="text-align:center"><?php echo $message; ?></p>
    <div class="b-container">
      <div class="tabla">
        <h2>Tus libros    [<?php echo $books_count?>]</h2>
        <div id="cont-tabla"><?php echo (!empty($table)) ? $table : '';?></div>
      </div>
      <div class="form-add">
        <h2>Agrega más libros a tu biblioteca</h2>
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
          <input type="number" placeholder="ISBN" name="isbn">
          <input type="text" placeholder="Autor" name="autor" required>
          <input type="text" placeholder="Título" name="titulo" required>
          <input type="text" placeholder="Categoría o género" name="categoria">
          <input type="number" placeholder="año" name="año">
          <input type="submit" value="añadir">
        </form>
        <p><?php echo $message?></p>
      </div>
    </div>
    </main>
  </main>

</body>
</html>


