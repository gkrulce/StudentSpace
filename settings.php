<?php
  include('php/session.php');
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
            <li role="presentation"><a href="view.php">View</a></li>
            <li role="presentation"><a href="create.php">Create</a></li>
            <li role="presentation"><a href="chat.php">My Spaces</a></li>
            <li role="presentation" class="active"><a href="settings.php">Settings</a></li>
            <li role="presentation" id="nav-accent"><a href="feedback.php">Feedback</a></li>
            <li role="presentation"><a href="logout.php">Logout</a></li>
          </ul>
        </div><!--/.nav-collapse -->
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
      <legend>Email settings!</legend>

      <!-- Multiple Checkboxes -->
      <div class="form-group">
        <label class="col-md-4 control-label" for="emailPref">Be emailed when new groups are created</label>
        <div class="col-md-4">

          <?php
            $i = 0;
            foreach($_SESSION['user']->getClasses($db) as $row) {
              echo '<div class="checkbox"><label for="emailPref-' . $i . '"><input type="checkbox" name="emailPref[]" id="emailPref-' . $i . '" value="' . $row['class_id'] . '"';
              if($row["desires_email"] == "1") {
                echo ' checked';
              }
              echo'>' . $row['class_name'] . '</label></div>';
              $i++;
            }
          ?>
        </div>
      </div>

      <!-- Button -->
      <div class="form-group">
        <label class="col-md-4 control-label" for="singlebutton"></label>
        <div class="col-md-4">
          <button id="singlebutton" name="singlebutton" class="btn btn-primary">Save</button>
        </div>
      </div>

      </fieldset>
      </form>

    </div>
    

    <!-- Bootstrap core JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

  </body>
</html>
