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

    <title>&#9829 Space @ UCSD</title>

    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="css/materialize.css"  media="screen,projection"/>
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
    <script src="js/filters.js"></script>
  </head>

  <body ng-controller="ChatCtrl">

  <!-- Model displays after study group creation -->
  <div id="createModal" class="modal">
    <div class="modal-content">
      <h4>You've joined a group!</h4>
      <p>Pro tips: </p>
      <ol>
        <li>Introduce yourself (name, major, etc...). It'll break the ice! </li>
        <li>Figure out a time and place to meet up. </li>
        <li>Prepare before coming: don't show up clueless. You'll only learn if you're willing to put in the effort.</li>
      </ol>
      
    </div>
    <div class="modal-footer">
      <a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Lets do this!</a>
    </div>
  </div>

    <nav class="blue darken-4">
      <a href="#" data-activates="slide-out" class="button-collapse">
        <i class="mdi-navigation-menu"></i>
      </a>
      <a href="#" class="brand-logo">Space</a>
      <!-- Top header-->
      <ul class="navbar right hide-on-med-and-down">
        <li><a href="view.php">View</a></li>
        <li><a href="create.php">Create</a></li>
        <li class="active"><a href="chat.php">Chat</a></li>
        <li><a href="settings.php">Settings</a></li>
        <li><a class="blue-text text-lighten-1" href="feedback.php">Feedback</a></li>
      </ul>

      <!-- Fixed side nav for large screens -->
      <ul id="class-nav" class="side-nav fixed right hide-on-med-and-down">
        <li ng-repeat="r in rooms" role="presentation">
          <a href="" class="my-tab" data-toggle="tab"
              ng-click="scrollDownChat($index); switchActiveTab($index)">
            {{r.group_name}}
            <div class="btn class remove red darken-2 circle"
                ng-click="leaveGroup($index)">
              <i class="mdi-content-clear small"></i>
            </div>
            <div class="btn class info blue accent-3 circle"
                ng-click="($index)">
              <i class="mdi-action-info-outline small"></i>
            </div>
          </a>
        </li>
      </ul>

      <!-- Moving side nav for small screens -->
      <ul id="slide-out" class="side-nav">
        <!-- Standard links -->
        <li><a href="view.php">View</a></li>
        <li><a href="create.php">Create</a></li>
        <li class="active"><a href="chat.php">Chat</a></li>
        <li><a href="settings.php">Settings</a></li>
        <li><a href="feedback.php">Feedback</a></li>

        <!-- Rooms -->
        <li class="blue accent-3">My Rooms</li>
        <li ng-repeat="r in rooms" role="presentation">
          <a href="" class="my-tab" data-toggle="tab" ng-click="scrollDownChat();
              switchActiveTab($index)">
            {{r.group_name}}
            <div class="btn class remove red darken-2 circle"
                ng-click="leaveGroup($index)">
              <i class="mdi-content-clear small"></i>
            </div>
            <div class="btn class info blue accent-3 circle"
                ng-click="($index)">
              <i class="mdi-action-info-outline small"></i>
            </div>
          </a>
        </li>
      </ul>
    </nav>

    <!-- Main pane -->
    <div class="chat-area">
      <!-- Holds the messages-->
      <div id="message-pane">
        <div class="tab-content">
          <div ng-repeat="r in rooms" class="tab-pane" id="tab{{$index}}"
              ng-if="isTabActive($index)">
            <div class="chat-block" ng-repeat="m in r.messages">
              <a class="btn tooltipped username circle amber darken-1"
                  data-position="left" data-delay="50" data-tooltip="{{m.username}}"
                  title="{{m.username}}"
                  ng-if="m.username != username">
                {{m.username[0].toUpperCase()}}
              </a>
              <div ng-class="{'sent blue accent-3': m.username == username,
                  'arrive grey lighten-3': m.username != username}" class="chat-bubble">
                {{m.message}}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Chat input box -->
      <form class="input-box" onsubmit="chatReset(); return false;" ng-submit="sendMessage()" action="#">
        <div class="input-field">
          <input class="resetOnSubmit" type="text" ng-init="inputText = ''"
              ng-model="inputText" placeholder="Enter a message..."/>
          <a ng-init="isAnonymous = false" ng-click="isAnonymous = !isAnonymous"
              ng-class="{'grey-text grey-lighten-3': isAnonymous}"
              title="Become {{!isAnonymous ? 'anonymous' : 'visible as ' +  username}}"
              class="btn sender blue-text blue-accent-3">
            <i class="mdi-action-visibility small"></i>
          </a>
          <button type="submit" class="btn sender blue-text text-accent-3">
            <i class="mdi-content-send small"></i>
          </button>
          <div class="anon-box">
            <!--
            <p>
              <input type="checkbox" ng-model="isAnonymous" id="anon-check" />
              <label for="anon-check"></label>
            </p>
-->
          </div>
        </div>
      </form>
    </div> <!-- End of .chat-area -->
    
    <!-- Import jQuery before boostrap/materialize -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>

    <script>
      <?php
        if(isset($_GET['action']) && $_GET['action'] == 'join') {
          echo '$("#createModal").openModal();';
        }
      ?>
    </script>

  </body>
</html>
