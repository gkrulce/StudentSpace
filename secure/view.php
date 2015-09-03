<?php
  include('../php/session.php');
  echo $_SESSION['user']->getName();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>&#9829 StudentSpace</title>

    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/js/materialize.min.js"></script>

    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/css/materialize.min.css">
    <!-- Materialize icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Google fonts -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300|Indie+Flower' rel='stylesheet' type='text/css'>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/main.css">
  </head>

  <body>
    <nav>
      <div class="nav-wrapper blue darken-4">
        <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
        <a href="#" class="brand-logo">space<span class="amber-text text-darken-1">@ucsd</span></a>
        <ul id="nav-mobile" class="right hide-on-med-and-down">
          <li class="active"><a href="view.php">View</a></li>
          <li><a href="create.php">Create</a></li>
          <li><a href="chat.php">My Spaces</a></li>
          <li><a href="settings.php">Settings</a></li>
          <li id="nav-accent"><a href="feedback.php">Feedback</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
        <ul id="mobile-demo" class="side-nav right">
          <li class="active"><a href="view.php">View</a></li>
          <li><a href="create.php">Create</a></li>
          <li><a href="chat.php">My Spaces</a></li>
          <li><a href="settings.php">Settings</a></li>
          <li id="nav-accent"><a href="feedback.php">Feedback</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </div>
    </nav>

    <div class="container">
      <div class="alert alert-danger hide" role="alert"> Study group not joined. </div>
      <h2 class="text-center">Join a StudentSpace</h2>
      <h2><small></small></h2>
      <table class="table table-bordered">
        <tr class="blue accent-3">
          <th>Class Name</th>
          <th>Title</th>
          <th>Time</th>
          <th>Date</th>
          <th>More Information</th>
        </tr>
        <?php
          foreach($_SESSION['user']->getAllStudyGroups($db) as $row)
          {
            $date = new DateTime($row['date']);
            echo '<tr class="data-row"><td>' . $row['class_name'] . '</td>';
            echo '<td>' . $row['group_name'] . '</td>';

            echo '<td>' . $row['time'] . '</td><td>' . $date->format("F, D j") . '</td>';
            echo '<td><button type="button" class="btn light-blue accent-3">+<i class="material-icons small system_update_alt"></i></button></td>';
            echo '</tr>';
            echo '<tr><td colspan="10" class="expandable"><div class="secret">Group size: ';
            for($i = 0 ; $i < $row['group_size']; $i++)
            {
              echo 'X';
            }
            echo $row['long_desc'] . '<a role="button" class="btn blue accent-4 joinStudyGroupBtn" id="' . $row['group_id'] . '" href="#">Join</a></div></td></tr>';
          }
        ?>
      </table>
    </div>
    <script src="../js/view.js"></script>
  </body>
</html>
