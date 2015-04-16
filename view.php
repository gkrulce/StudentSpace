<?php
  include('php/session.php');
  include('php/db.php');
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

    <title>&#9829 StudyTree</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

    <!-- Font awesome CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

    <!-- Google fonts -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300|Indie+Flower' rel='stylesheet' type='text/css'>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/main.css">

  </head>

  <body>

    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.html">StudyTree</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li role="presentation" class="active"><a href="view.php">View</a></li>
            <li role="presentation"><a href="create.php">Create</a></li>
            <li role="presentation"><a href="chat.html">Chat</a></li>
            <li role="presentation"><a href="settings.php">Settings</a></li>
            <li role="presentation"><a href="logout.php">Logout</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">
      <h2 class="text-center"> Current Study Groups! </h2>
      <table class="table table-bordered">
        <tr>
          <th>Class Name</th>
          <th>Group Size</th>
          <th>Date</th>
          <th>Time</th>
        </tr>
        <?php
          foreach($_SESSION['user']->getCurrentStudyGroups($db) as $row)
          {
            $date = new DateTime($row['start_time']);
            echo '<tr><td>' . $row['name'] . '</td><td>';

            for($i = 0 ; $i < $row['group_size']; $i++)
            {
              echo '<span class="fa fa-user"></span>';
            }
            echo '</td><td>' . $date->format("F, D j") . '</td><td>' . $date->format("g:i A") . '</td>';
            echo '</tr>';
          }
        ?>
      </table>
    </div>

    <div class="container">
      <h2 class="text-center"> Need a study group? </h2>
      <h3 class="text-center"> Join one! </h3>
      <table class="table table-bordered">
        <tr>
          <th>Class Name</th>
          <th>Group Size</th>
          <th>Date</th>
          <th>Time</th>
          <th>More Information</th>
        </tr>
        <?php
          foreach($_SESSION['user']->getAllStudyGroups($db) as $row)
          {
            $date = new DateTime($row['start_time']);
            echo '<tr><td>' . $row['name'] . '</td><td>';

            for($i = 0 ; $i < $row['group_size']; $i++)
            {
              echo '<span class="fa fa-user"></span>';
            }
            echo '</td><td>' . $date->format("F, D j") . '</td><td>' . $date->format("g:i A") . '</td>';
            echo '<td><a role="button" class="btn btn-primary" href="#">Join<span class="fa fa-external-link"></span></a></td></tr>';
          }
        ?>
      </table>
    </div>

    <!-- Bootstrap core JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

  </body>
</html>
