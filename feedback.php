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
            <li role="presentation"><a href="settings.php">Settings</a></li>
            <li role="presentation" class="active" id="nav-accent"><a href="feedback.php">Feedback</a></li>
            <li role="presentation"><a href="logout.php">Logout</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

<div class="container">
  <?php
    if(isset($_POST['singlebutton'])) {
      //TODO SEND EMAIL
      echo '<div class="alert alert-success" role="alert">Feedback successfully submitted!</div>';
      unset($_POST);
    }
  ?>
  <form class="form-horizontal" action="feedback.php" method="post">
  <fieldset>

  <!-- Form Name -->
  <legend>Feedback</legend>

  <!-- Textarea -->
  <div class="form-group">
    <label class="col-md-4 control-label" for="feedback">This form directly contacts our development team.</label>
    <div class="col-md-4">                     
      <textarea class="form-control" id="feedback" name="feedback"></textarea>
    </div>
  </div>

  <!-- Button -->
  <div class="form-group">
    <label class="col-md-4 control-label" for="singlebutton"></label>
    <div class="col-md-4">
      <button id="singlebutton" name="singlebutton" class="btn btn-primary">Submit!</button>
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
