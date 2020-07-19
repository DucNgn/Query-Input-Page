<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width" />
    <title>Bookstore Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
  <style>
  table {
    width: 60%;
  }
  </style>
  </head>
<body>
  <form accept-charset="utf-8" action="#" method='POST'>
    <div class="form-group">
      <center>
        <h1> Insert MySQL query </h1>
        <textarea class="form-control" required name="sqlRequest" placeholder="SQL query" rows="3"></textarea>
        <br>
        <button class="btn btn-primary" type="submit">Submit</button>
      </center>
    </div>
  </form>

  <?php
  // Function to check if querry contains words in blacklist
  function containsProhibited($str)
  {
    $blacklist = array("CREATE", "ALTER", "DROP", "BACKUP", "INSERT");
    foreach($blacklist as $a) {
      if (stripos($str,$a) !== false) return true;
    }
    return false;
  }

  // Get credentials
  $creds = json_decode(file_get_contents('credentials.json'), true);
  $host_name = $creds["HOSTNAME"];
  $user = $creds["DB_USER"];
  $password = $creds["DB_PASSWORD"];
  $DBname = $creds["DB_NAME"];

  // Connect to the database
  $conn = mysqli_connect($host_name, $user, $password, $DBname);
  if(!$conn){
    echo "
    <div class=\"alert alert-danger\" role=\"alert\">
      Cannot connect to the database. Please try again
    </div>
    ";
    die("Connection failed: ".mysqli_connect_error());
  }

  // Handle POST request
  if(isset($_POST['sqlRequest'])) {
    $query = $_POST['sqlRequest']; 

    // Check for modification attempt
    if(containsProhibited($query)) {
      echo "
      <div class=\"alert alert-danger\" role=\"alert\">
        Modification of the database is not allowed.
      </div>
      ";
      exit(0);
    }  

    $result = mysqli_query($conn,$query);

    // Check for valid SQL query
    if(!empty($result)) {
      // Load and display table
      $fields = array();
      echo "<center><table>";
      // Headers
      echo "<tr class=\"table-primary\">";
      while ($property = mysqli_fetch_field($result)) {
        echo '<th>' . $property->name . '</th>';  
        array_push($fields, $property->name); 
      }
      echo '</tr>';
  
      // Body
      while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        foreach ($fields as $item) {
            echo '<td>' . $row[$item] . '</td>'; 
        }
        echo '</tr>';
      } 
      echo "</table></center>";
      
    } else {
      echo "
        <div class=\"alert alert-danger\" role=\"alert\">
          Your query is invalid. Please try again
        </div>
      ";

    }

  }

  mysqli_close($conn);
?>

</body>
</html>
