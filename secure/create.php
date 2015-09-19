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
    <link href='https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300|Indie+Flower' rel='stylesheet' type='text/css'>
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
          <li class="active"><a href="create.php">Create</a></li>
          <li><a href="chat.php">My Spaces</a></li>
          <li><a href="settings.php">Settings</a></li>
          <li id="nav-accent"><a href="feedback.php">Feedback</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
        <ul id="mobile-demo" class="side-nav right">
          <li><a href="view.php">View</a></li>
          <li class="active"><a href="create.php">Create</a></li>
          <li><a href="chat.php">My Spaces</a></li>
          <li><a href="settings.php">Settings</a></li>
          <li id="nav-accent"><a href="feedback.php">Feedback</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </div>
    </nav>

<div class="container">
<?php

  function validateDate($date, $format) {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
  }

  function validateTime($date, $offset) {
    $date->add(new DateInterval('PT' . $offset . 'H'));
    $now = new DateTime();
    $now->sub(new DateInterval('PT7H'));
    // Debugging information left here to test the time on the server
    //var_dump($date);
    //var_dump($now);
    return $date >= $now;
  }

  if(isset($_POST['submit_button']))
  {
    $date = new DateTime($_POST['date']);
    $offset = Procedures::getOffset($db, $_POST['time']);
    if(!validateDate($_POST['date'], 'Y-m-d'))
    {
      echo '<div class="alert alert-danger" role="alert">The date must be given in the format YYYY-MM-DD (You gave ' . $_POST['date'] . ') </div>';
    }else if(!validateTime($date, $offset)) {
      echo '<div class="alert alert-danger" role="alert">You tried to create a study group in the past.</div>';
    }else if($_SESSION['user']->createStudyGroup($db, $_POST))
    {
      unset($_POST);
      header('Location: chat.php?action=join');
    }else
    {
      echo '<div class="alert alert-danger" role="alert">There was an error upon study group creation. Sorry!</div>';
    }
  }
?>
  <div class="create-form">
  <form action="create.php" method="post" class="form-horizontal">
  <fieldset>

  <!-- Form Name -->
  <legend>Create a own study group to study on your time!</legend>

  <!-- Text input-->
  <div class="form-group">
    <label class="col-md-4 control-label" for="date">Date</label>  
    <div class="col-md-4">
      <input id="date" name="date" type="date" class="form-control input-md" required="" value="<?php if (isset($_POST['date'])) { echo $_POST['date']; } else { echo date('Y-m-d'); } ?>">
    </div>
  </div>

  <!-- Select Basic -->
  <div class="form-group">
    <label class="col-md-4 control-label" for="class_id">Time</label>
    <div class="col-md-4">
      <div class="input-field col s12">
        <select class="browser-default" name="time">

          <?php
          foreach(Procedures::getStudyTimes($db) as $row)
          {
            echo '<option value="'. $row["id"].'">' . $row["name"] . ' ' . $row["time_range"] . '</option>';
          }
          ?>
        </select>
      </div>
    </div>
  </div>

  <!-- Select Basic -->
  <div class="form-group">
    <label class="col-md-4 control-label" for="class_id">Class</label>
    <div class="col-md-4">
      <div class="input-field col s12">
        <select name="class_id" class="browser-default">

          <?php
          foreach($_SESSION['user']->getClasses($db) as $row)
          {
            echo '<option value="'. $row["class_id"].'">' . $row["class_name"] . '</option>';
          }
          ?>
        </select>
      </div>
    </div>
  </div>

  <!-- Text input-->
  <div class="form-group">
    <label class="col-md-4 control-label" for="shortTitle">Title</label>  
    <div class="col-md-4">
      <input id="shortTitle" name="short_desc" type="text" placeholder="eg. Midterm 1 or Homework 2" class="form-control input-md" required="" value="<?php if(isset($_POST['short_desc'])) {echo $_POST['short_desc'];} ?>">
    </div>
  </div>

  <!-- Textarea -->
  <div class="form-group">
    <label class="col-md-4 control-label" for="textarea">Agenda</label>
    <div class="col-md-4">                     
      <textarea class="form-control" id="agenda" name="long_desc" placeholder="eg. Solve homework problems or quiz each other on vocabulary terms."><?php if(isset($_POST['long_desc'])) {echo $_POST['long_desc'];} ?></textarea>
      <script>
        CKEDITOR.replace('long_desc');
      </script>
    </div>
  </div>

  <!-- Button -->
  <div class="form-group">
    <label class="col-md-4 control-label" for="submit_button"></label>
    <div class="col-md-4">
      <button id="submit_button" name="submit_button" class="btn light-blue accent-3">Create</button>
    </div>
  </div>

  </fieldset>
  </form>


  </div>
</div>

  </body>
</html>
