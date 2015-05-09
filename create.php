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

    <script src="//cdn.ckeditor.com/4.4.7/basic/ckeditor.js"></script>
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
            <li role="presentation" class="active"><a href="create.php">Create</a></li>
            <li role="presentation"><a href="chat.php">Chat</a></li>
            <li role="presentation"><a href="settings.php">Settings</a></li>
            <li role="presentation" id="nav-accent"><a href="feedback.php">Feedback</a></li>
            <li role="presentation"><a href="logout.php">Logout</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

<div class="container">
<?php

  function validateDate($date, $format)
  {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
  }

  if(isset($_POST['submit_button']))
  {
    if(!validateDate($_POST['date'], 'Y-m-d'))
    {
      echo '<div class="alert alert-danger" role="alert">The date must be given in the format YYYY-MM-DD (You gave ' . $_POST['date'] . ') </div>';
    } else if(!validateDate($_POST['start_time'], 'H:i'))
    {
      echo '<div class="alert alert-danger" role="alert">The time must be given in the format HH:MM (You gave ' . $_POST['start_time'] . ') </div>';
    }else if($_SESSION['user']->createStudyGroup($db, $_POST))
    {
      echo '<div class="alert alert-success" role="alert">Study Group successfully created. Go to the chat and introduce yourself!</div>';
      unset($_POST);
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
  <legend>Create your own study group to study at YOUR convenience!</legend>

  <!-- Text input-->
  <div class="form-group">
    <label class="col-md-4 control-label" for="date">Date</label>  
    <div class="col-md-4">
      <input id="date" name="date" type="date" class="form-control input-md" required="" value="<?php if (isset($_POST['date'])) { echo $_POST['date']; } else { echo date('Y-m-d'); } ?>">
    <span class="help-block">Which day do you want to meet?</span>  
    </div>
  </div>

  <!-- Text input-->
  <div class="form-group">
    <label class="col-md-4 control-label" for="start_time">Start Time</label>  
    <div class="col-md-4">
      <input id="start_time" name="start_time" type="time" class="form-control input-md" required="" value="<?php if(isset($_POST['start_time'])) {echo $_POST['start_time'];} else {echo "20:00";} ?>" step=900>
    <span class="help-block">What time do you want to meet?</span>  
    </div>
  </div>

  <!-- Select Basic -->
  <div class="form-group">
    <label class="col-md-4 control-label" for="class_id">Class</label>
    <div class="col-md-4">
      <select id="classSelectId" name="class_id" class="input-xlarge">

        <?php
        foreach($_SESSION['user']->getClasses($db) as $row)
        {
          echo '<option value="'. $row["class_id"].'">' . $row["class_name"] . '</option>';
        }
        ?>
      </select>
    </div>
  </div>

  <!-- Text input-->
  <div class="form-group">
    <label class="col-md-4 control-label" for="shortTitle">Title</label>  
    <div class="col-md-4">
      <input id="shortTitle" name="short_desc" type="text" placeholder="eg. Midterm 1 or Homework 2" class="form-control input-md" required="" value="<?php if(isset($_POST['short_desc'])) {echo $_POST['short_desc'];} ?>">
    <span class="help-block">What are you studying?</span>  
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
      <span class="help-block">How are you studying? List a few specific actions you want to accomplish during this study group.</span>  
    </div>
  </div>

  <!-- Button -->
  <div class="form-group">
    <label class="col-md-4 control-label" for="submit_button"></label>
    <div class="col-md-4">
      <button id="submit_button" name="submit_button" class="btn btn-primary">Create</button>
    </div>
  </div>

  </fieldset>
  </form>


  </div>
</div>
    

    <!-- Bootstrap core JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

  </body>
</html>
