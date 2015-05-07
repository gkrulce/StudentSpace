<?php
  include('php/session.php');
?>
<!DOCTYPE html>
<html lang="en" class="full-height" ng-app="ChatApp">
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
    <link rel="stylesheet" href="css/chat.css">


    <!-- Angular JS -->
    <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>

    <!-- Socket IO JavaScript -->
    <script src="https://cdn.socket.io/socket.io-1.3.5.js"></script>

    <script>
      var userId = "<?php echo $_SESSION['user']->getHash() ?>";
    </script>

    <!-- Chat Javascript -->
    <script src="js/chat.js"></script>

  </head>

  <body class="full-height">

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
            <li role="presentation" class="active"><a href="chat.php">Chat</a></li>
            <li role="presentation"><a href="settings.php">Settings</a></li>
            <li role="presentation"><a href="logout.php">Logout</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

  <body ng-controller="ChatCtrl" class="full-height">

    <div class="row full-height">
      <div class="col-xs-2">
        <ul class="nav nav-pills nav-stacked gold-pills">
            <li ng-repeat="r in rooms" role="presentation"><a href="#tab{{$index}}" data-toggle="tab">{{r.group_name}}</a></li>
        </ul>
      </div>
      <div class="col-xs-10 chat-area full-height">
        <div class="tab-content">
          <div ng-repeat="r in rooms" class="tab-pane" id="tab{{$index}}">

            <div class="chat-block" ng-repeat="m in r.messages">
              <div class="chat-bubble sent" ng-if="m.username == username">
                <p>{{m.message}}</p>
              </div>

              <div class="chat-bubble arrive" ng-if="m.username !== username">
                <p>{{m.message}}</p>
              </div>
            </div>

          </div>
        </div>
        <form onsubmit="chatReset(); return false;" ng-submit="sendMessage()" >
          <div class="input-box">
            <input class="resetOnSubmit" type="text" ng-model="inputText" placeholder="Enter message here..."/>
            <input class="btn btn-primary" type="submit" value="Send">
            <input type="checkbox" ng-model="isAnonymous"> Send Anonymously
          </div>
        </form>
      </div>
    </div>
    

    <!-- Bootstrap core JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

  </body>
</html>
