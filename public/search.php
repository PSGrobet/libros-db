<?php

require_once "config.php";
$message = "";

if(isset($_POST['q'])) {
  $s_query = "%".htmlspecialchars($_POST['q'])."%";

  // Prepare statement
  $sql = "SELECT isbn, autor, titulo, categoria, año, user_id FROM books_main WHERE isbn LIKE ? OR autor LIKE ? OR titulo LIKE ? OR categoria LIKE ?;";
  if(!$stmt = $mysqli->prepare($sql)) {
    echo "error <br>";
  } else {
    $stmt->bind_param("ssss", $s_query, $s_query, $s_query, $s_query);

    if($stmt->execute()) {
      $result = $stmt->get_result();
      $rows = $result->num_rows;
      if(!$rows < 1) {
        $table = "<p>Resultados: ".$result->num_rows."</p>
        <table class='result-table'>
        <tr>
          <th>ISBN</th>
          <th>Autor</th>
          <th>Titulo</th>
          <th>Categoría</th>
          <th>Año</th>
          <th>Usuario</th>
        </tr>";

        while($row = $result->fetch_array()) {
          // $res = match_field('autor');
          // echo $res;
          $new_row = "<tr>"
                    ."<td>".$row['isbn']."</td>"
                    ."<td class=".match_field('autor').">".$row['autor']."</td>"
                    ."<td class=".match_field('titulo').">".$row['titulo']."</td>"
                    ."<td class=".match_field('categoria').">".$row['categoria']."</td>"
                    ."<td>".$row['año']."</td>"
                    ."<td>".$row['user_id']."</td>"
                    ."</tr>";
          $table .= $new_row;
          }
          $table .= "</table>";

      } else {
        $message = "Lo siento, no encontramos ningún resultado.";
      }  
    }      
  }
}

function match_field($field) {
  global $row;
  $query = strtolower($_POST['q']);
  $field_result = strtolower($row["$field"]);
  if(strstr($field_result, $query)) { // strstr() búsqueda simple de un string en otro, devuelve true o false.
    return "match";
  } else return "";
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

  <link rel="stylesheet" href="./css/styles.css?v=1.6">
  <link rel="stylesheet" href="./css/styles-search.css?v=1.3">
  <title>Búsqueda</title>
</head>
<body>
  <div class="main">
    <div class="page-header">
      <h2 class="titulo">Busca un libro</h2>
    </div>
    <div class="container-search">
      <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
        <div class="form-group">
          <input type="search" placeholder="Busca ISBN, autor o título" name="q">
          <button type="submit"><img src="images/search2.png"/></button>
        </div>
      </form>
      
    </div>
    <div id="search-message"><p><?php echo $message; ?></p></div>
    <div id="cont-tabla"><?php echo (!empty($table)) ? $table : '';?></div>
  </div>
  
</body>
</html>