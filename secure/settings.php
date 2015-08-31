<?php
  include('../php/session.php');
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
          <li><a href="view.php">View</a></li>
          <li><a href="create.php">Create</a></li>
          <li><a href="chat.php">My Spaces</a></li>
          <li class="active"><a href="settings.php">Settings</a></li>
          <li id="nav-accent"><a href="feedback.php">Feedback</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
        <ul id="mobile-demo" class="side-nav right">
          <li><a href="view.php">View</a></li>
          <li><a href="create.php">Create</a></li>
          <li><a href="chat.php">My Spaces</a></li>
          <li class="active"><a href="settings.php">Settings</a></li>
          <li id="nav-accent"><a href="feedback.php">Feedback</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </div>
    </nav>

    <div class="container">
      <?php 
      if(isset($_POST['singlebutton'])) {
        if($_SESSION['user']->updateEmailPreferences($db, $_POST['emailPref']))
        {
          echo '<div class="alert alert-success" role="alert">Email preferences successfully updated!</div>';
        }else
        {
          echo '<div class="alert alert-danger" role="alert">There was an error while updating your email preferences</div>';
        }
      }
      ?>

      <form class="form-horizontal" action="settings.php" method="post">
      <fieldset>
      <!-- Form Name -->
      <legend>Email settings</legend>

      <!-- Multiple Checkboxes -->
      <div class="form-group">
        <label class="col-md-4 control-label" for="emailPref">Be emailed when new groups are created</label>
        <div class="col-md-4">

          <?php
            $i = 0;
            foreach($_SESSION['user']->getClasses($db) as $row) {
              echo '<p><input type="checkbox" name="emailPref[]" id="emailPref-' . $i . '" value="' . $row['class_id'] . '"';
              if($row["desires_email"] == "1") {
                echo ' checked';
              }
              echo'><label for="emailPref-' . $i . '">' . $row['class_name'] . '</label></p>';
              $i++;
            }
          ?>
        </div>
      </div>

      <!-- Button -->
      <div class="form-group">
        <label class="col-md-4 control-label" for="singlebutton"></label>
        <div class="col-md-4">
          <button id="singlebutton" name="singlebutton" class="btn light-blue accent-3">Save</button>
        </div>
      </div>

      </fieldset>
      </form>

    </div>

  </body>
</html>
