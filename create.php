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
            <li role="presentation"><a href="view.php">View</a></li>
            <li role="presentation" class="active"><a href="create.php">Create</a></li>
            <li role="presentation"><a href="chat.html">Chat</a></li>
            <li role="presentation"><a href="settings.php">Settings</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>


<div class="container">
<?php
  if(isset($_POST['submit_button']))
  {
    $assocArray = array();
    $assocArray['start_date_time'] = $_POST['date'] . ' ' . $_POST['start_time'];
    $assocArray['short_desc'] = $_POST['short_desc'];
    $assocArray['long_desc'] = $_POST['long_desc'];
    $assocArray['class_id'] = $_POST['class_id'];
    if($_SESSION['user']->createStudyGroup($db, $assocArray))
    {
      echo '<div class="alert alert-success" role="alert">Study Group successfully created</div>';
    }else
    {
      echo '<div class="alert alert-danger" role="alert">There was an error upon study group creation. Sorry!</div>';
    }
    unset($_POST);
  }
?>
<h2 class="text-center"> Can't find a Study Group? </h2>
<form action = "create.php" method="post" class="form-horizontal">
    <fieldset>

    <!-- Form Name -->
    <legend>Make your own!</legend>

    <!-- Date input-->
    <div class="control-group">
      <label class="control-label" for="dateid">Date</label>
      <div class="controls">
	<input type="date" id = "dateid" name="date" value="<?php echo date('Y-m-d'); ?>" class="input-xlarge">
	<p class="help-block">Pick a convenient date proximate to class events</p>
      </div>
    </div>

    <!-- Start time input-->
    <div class="control-group">
      <label class="control-label" for="startTimeId">Start Time</label>
      <div class="controls">
	<input type="time" name="start_time" id="startTimeId" class="input-xlarge" step=900>
	<p class="help-block">Study groups do not need an end time</p>
      </div>
    </div>

    <!-- Class selection -->
    <div class="control-group">
      <label class="control-label" for="classSelectId">Class Selection</label>
      <div class="controls">
	<select id="classSelectId" name="class_id" class="input-xlarge">

	  <?php
		foreach($_SESSION['user']->getClasses($db) as $row)
		{
			echo '<option value="'. $row["id"].'">' . $row["name"] . '</option>';
		}
	  ?>
	</select>
      </div>
    </div>

    <!-- Title input-->
    <div class="control-group">
      <label class="control-label" for="shortTitle">Title</label>
      <div class="controls">
        <input id="shortTitle" name="short_desc" type="text" class="input-xlarge">
        <p class="help-block"> What are you studying?</p>
      </div>
    </div>

    <!-- Angenda input -->
    <div class="control-group">
      <label class="control-label" for="agenda">Agenda</label>
      <div class="controls">                     
        <textarea id="agenda" name="long_desc" class="agenda-text">Plan how you will study here. Planning ahead improves study group success.</textarea>
      </div>
    </div>

    <!-- Submit Button -->
    <div class="control-group">
      <div class="controls">
	<br>
	<button id="singlebutton" name="submit_button" class="btn btn-primary" type="submit">Submit!</button>
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
