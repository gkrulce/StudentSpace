<?php
  include('../php/session.php');
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

    <title>&#9829 StudentSpace</title>

    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/js/materialize.min.js"></script>
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/css/materialize.min.css">

    <!-- Font awesome CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <!-- Materialize icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Google fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300|Indie+Flower' rel='stylesheet' type='text/css'>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/chat.css">
    <!-- Angular JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular-animate.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular-sanitize.js"></script>
    <!-- Moment.js for time formatting -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js"></script>
    <!-- Socket IO JavaScript -->
    <script src="https://cdn.socket.io/socket.io-1.3.5.js"></script>

    <script>
      var userId = "<?php echo $_SESSION['user']->getHash() ?>";
    </script>

    <!-- Chat Javascript -->
    <script src="../js/chat.js"></script>
    <script src="../js/filters.js"></script>
  </head>

  <body ng-controller="ChatCtrl">

  <!-- Model displays after study group creation -->
  <div id="createModal" class="modal">
    <div class="modal-content">
      <h4>Welcome to your Space!</h4>
      <p>Pro tips: </p>
      <ol>
        <li><strong>Introduce yourself</strong> (name, major, etc...). It'll break the ice! </li>
        <li>Figure out a <strong>time and place</strong> to meet up. </li>
        <li>Prepare before coming: <strong>don't show up clueless</strong>. You'll only learn if you're willing to put in the effort.</li>
        <li>Don't be discouraged by weaker students. Studies show that <strong>group diversity improves learning</strong>.</li>
      </ol>
    </div>
    <div class="modal-footer">
      <a href = "chat.php" class=" modal-action modal-close waves-effect waves-green btn-flat">Lets do this!</a>
    </div>
  </div>

    <nav class="blue darken-4">
      <a href="#" data-activates="slide-out" class="button-collapse">
        <i class="mdi-navigation-menu"></i>
      </a>
      <a href="#" class="brand-logo">space<span class="amber-text text-darken-1">@ucsd</span></a>
      <a class="curr-chat">{{rooms[currTab].group_name}}</span></a>
      <!-- Top header-->
      <ul class="navbar right hide-on-med-and-down">
        <li><a href="view.php">View</a></li>
        <li><a href="create.php">Create</a></li>
        <li class="active"><a href="chat.php">My Spaces</a></li>
        <li><a href="settings.php">Settings</a></li>
        <li><a class="blue-text text-lighten-1" href="feedback.php">Feedback</a></li>
        <li><a href="logout.php">Logout</a></li>
<!--
        <li><a class="blue-text text-lighten-1" ng-click="tellMeStuff()">TELLMESTUFF</a></li>
-->
      </ul>

      <!-- Fixed side nav for large screens -->
      <ul id="class-nav" class="side-nav fixed right hide-on-med-and-down">
        <li ng-repeat="r in rooms" role="presentation">
          <a class="my-tab" href="#{{$index}}"
              ng-click="switchActiveTab($index)">
            {{r.group_name}}
            <div class="btn class grey lighten-2 grey-text text-darken-1"
                ng-if="r.is_study_group"
                ng-click="toggleDropdown(r.group_name, $event)">
              <i class="mdi-navigation-more-vert"></i>
            </div>
            <div class="btn class blue accent-3"
                ng-if="r.is_study_group"
                ng-click="toggleClassInfo(r.group_name, $event)">
              <i class="mdi-action-info-outline"></i>
            </div>
          </a>

          <!-- Dropdown -->
          <ul class="sp-dropdown white grey-text text-darken-4"
              ng-class="{'sp-dropdown--open': openedDropdownGroup == r.group_name}"
              ng-show="openedDropdownGroup == r.group_name">
            <li ng-click="leaveGroup($index)">Leave {{r.group_name}}</li>
          </ul> <!-- End dropdown -->
        </li>
      </ul>

      <!-- Moving side nav for small screens -->
      <ul id="slide-out" class="side-nav"
          ng-class="{'active': isTabActive($index)}">
        <li class="blue darken-4 side-nav-backout"
            ng-click="closeSideNav()">
          <i class="white-text mdi-navigation-arrow-back medium"></i>
        </li>
        <!-- Standard links -->
        <li><a href="view.php">View</a></li>
        <li><a href="create.php">Create</a></li>
        <li class="active"><a href="chat.php">Chat</a></li>
        <li><a href="settings.php">Settings</a></li>
        <li><a href="feedback.php">Feedback</a></li>
        <li><a href="logout.php">Logout</a></li>

        <!-- Rooms -->
        <li class="blue accent-3">My Rooms</li>
        <li ng-repeat="r in rooms" role="presentation">
          <a class="my-tab" href="#{{$index}}"
              ng-click="switchActiveTab($index)">
            {{r.group_name}}
            <div class="btn class grey lighten-3 grey-text text-darken-1"
                ng-if="r.is_study_group"
                ng-click="toggleDropdown(r.group_name, $event)">
              <i class="mdi-navigation-more-vert small"></i>
            </div>
            <div class="btn class blue accent-3"
                ng-if="r.is_study_group"
                ng-click="toggleClassInfo(r.group_name, $event)">
              <i class="mdi-action-info-outline small"></i>
            </div>
          </a>

          <!-- Dropdown -->
          <ul class="sp-dropdown white grey-text text-darken-4"
              ng-class="{'sp-dropdown--open': openedDropdownGroup == r.group_name}"
              ng-show="openedDropdownGroup == r.group_name">
            <li ng-click="leaveGroup($index)">Leave {{r.group_name}}</li>
          </ul> <!-- End dropdown -->
        </li>
      </ul>
    </nav>

    <!-- Main pane -->
    <div class="chat-area">
      <!-- Holds the messages-->
      <div class="message-pane">
        <div class="tab-content">
          <div ng-repeat="r in rooms" class="tab-pane" id="tab{{$index}}"
              ng-if="isTabActive($index)">
            <div class="chat-block" ng-repeat="m in r.messages">
              <div class="chat-row">
                <div class="grey-text text-lighten-1 chat-sig ani--grow"
                    ng-if="openedChatSig == $index">
                  {{m.username}} on {{m.time | dateify_time}}
                </div>
                <a class="username circle amber darken-1 btn tooltipped"
                    data-position="left" data-delay="50" data-tooltip="{{m.username}}"
                    title="{{m.username}}"
                    ng-if="m.username != username">
                  {{m.username[0].toUpperCase()}}
                </a>
                <div ng-class="{'chat-bubble--open-sig': openedChatSig == $index, 
                    'sent blue accent-3': m.username == username,
                    'arrive grey lighten-3': m.username != username}"
                    class="chat-bubble"
                    ng-click="toggleChatSig($index)">
                  {{m.message}}
                </div>
              </div>
            </div>

            <div class="chat-area--class-info ani--side-in"
                ng-if="openedClassInfo == r.group_name">
              <div class="btn waves-effect waves-dark grey lighten-3 grey-text text-darken-2"
                  ng-click="toggleClassInfo(r.group_name, $event)">
                <i class="mdi-action-highlight-remove"></i></div>
              <div class="class-head-card">
                <h1>{{r.group_name}}</h1>
                <div><h3 class="grey-text text-darken-1">
                  {{r.date | dateify_date}} | {{r.time}}</h3></div>
              </div>
              <div class="my-body">
                <p ng-bind-html="r.long_desc"></p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Chat input box -->
      <form class="input-box" onsubmit="chatReset(); return false;" ng-submit="sendMessage()" action="#">
        <div class="input-field">
          <input id="input--message" class="resetOnSubmit" type="text"
              ng-init="inputText = ''" ng-model="inputText"
              placeholder="Sending message as {{isAnonymous ? 'anonymous' : username}}..."/>
          <a ng-init="isAnonymous = false"
              ng-click="isAnonymous = !isAnonymous"
              ng-class="{'grey-text grey-lighten-3': isAnonymous}"
              title="Become {{isAnonymous ? 'visible as ' +  username : 'anonymous'}}"
              class="btn sender blue-text blue-accent-3">
            <i class="mdi-action-visibility small"></i>
          </a>
          <button type="submit" class="btn sender blue-text text-accent-3"
              ng-click="focusInput()">
            <i class="mdi-content-send small"></i>
          </button>

        </div>
      </form>
    </div> <!-- End of .chat-area -->

    <script>
      <?php
        if(isset($_GET['action']) && $_GET['action'] == 'join') {
          echo '$("#createModal").openModal();';
        }
      ?>
    </script>

  </body>
</html>
