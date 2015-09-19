function chatReset() {
  $(".resetOnSubmit").val("");
}
var socket = io(":500");

/*---------------------------------------------------------------------------*/
/* ANGULAR JS */
/*---------------------------------------------------------------------------*/
angular.module('ChatApp', ['ngAnimate', 'ngSanitize']).

controller('ChatCtrl', ['$scope', '$location', function($scope, $location) {
  $scope.rooms = [];
  $scope.id = userId;
  $scope.username = "";
  $scope.isAnonymous = false;
  $scope.currTab = 0;

  socket.on('connect', function() {
    console.log("Logging in with id: " + $scope.id);
    socket.emit('login', $scope.id);
  });

  socket.on('generic error', function() {
    console.log("There was an internal server error (500).");
  });

  socket.on('user information', function(msg) {
    $scope.username = msg;
  });

  /* Function that is called when user sends a message using the chat box form */
  $scope.sendMessage = function() {
    var loc;
    $('#class-nav > li').each(function(index) {
      if($(this).hasClass('active')) {
        loc = index;
      }
    });

    if(typeof loc == 'undefined') {
      console.log("Can't send a message if no chat rooms are active!!!");
    } else {
    
      if ($scope.inputText.length > 0) {
        var message = {"group_name": $scope.rooms[loc]["group_name"], "group_id": $scope.rooms[loc]["group_id"], "message": $scope.inputText, "username": $scope.username, "user_id": $scope.id, "isAnonymous": $scope.isAnonymous};
        if($scope.isAnonymous) {
          message["username"] = "Anonymous";
        }
        console.log("Sending message...");
        console.log( message);
        socket.emit('new message', message);

        $scope.inputText = ''; // Explicitly reset the input on DOM.
      } else {
        console.log("Cannot send message of length 0.");
      }
      //$scope.rooms[loc]['messages'] = $scope.rooms[loc]['messages'].concat(message);
      //console.log("Message sent. Rooms JSON...");
      //console.log($scope.rooms);
    }
  };

  socket.on('add rooms', function(msg) {
    console.log("Adding rooms");
    console.log(msg);
    $scope.rooms = msg;
    for(var i = 0 ; i < $scope.rooms.length ; i++) {
      $scope.rooms[i]['messages'] = [];
    }
    $scope.$apply();

    // Sets first room to active if tab is unspecified in URL.
    if(!$('#class-nav .active').length) {
      $('#class-nav li:first').addClass('active');
    }

    // Switches active tab depending on url
    var url = window.location.href;
    // Gets tab # at the end of url.
    // Depends on chat.php .my-tab a href!!!
    var currentTab = url.split('#/')[1]; 
    console.log('split url:', currentTab);
    if (currentTab != undefined && currentTab == parseInt(currentTab, 10)) {
      $scope.switchActiveTab(currentTab);
    }
  });

  socket.on('new messages', function(msg) {
    console.log("New messages!!");
    console.log(msg);
    for(var i = 0 ; i < msg.length ; i++) {
      var pos = -1;
      for(var j = 0 ; j < $scope.rooms.length ; j++) {
        if($scope.rooms[j]['group_id'] == msg[i]['group_id']) {
          pos = j;
          break;
        }
      }
      console.log("Message sent to room: " + pos);
      if(pos != -1) {
        $scope.rooms[pos]['messages'] = $scope.rooms[pos]['messages'].concat(msg[i]);
      }
    }
    $scope.$apply();
    $('.message-pane').scrollTop($('.message-pane')[0].scrollHeight);
    console.log("should have scrolled on message");
  });

  socket.on('disconnect', function() {
    $scope.rooms = [];
  });

  socket.on('refresh', function() {
    location.reload();
  });

  /* Scrolls to the bottom of the chat */
  $scope.scrollDownChat = function(tab) {
    console.log("scroll down chat on tab change...");
    console.log($('.message-pane')[0].scrollHeight);
    $('.message-pane').scrollTop($('.message-pane')[0].scrollHeight);
    console.log("scroll down chat on tab change DONE");
  };

  /* Leaves the study group that the user is in. */
  $scope.leaveGroup = function(data) {
    console.log("Leaving group: " + data);
    socket.emit('leave group', {"user_id": $scope.id, "group_id": $scope.rooms[data]['group_id']});
  };

  /* Grabs the GET parameters */
  function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
  };

  /* Switches the active tab of the class-nav */
  $scope.switchActiveTab = function(tab) {
    console.log("switch active tab fn", tab);
    $scope.currTab = tab;
    // Remove active class on all tabs
    $('#class-nav > li').removeClass('active');

    // Add active class on pressed tab.
    $($('#class-nav > li')[tab]).addClass('active');

    $scope.scrollDownChat(tab);
  }

  /* Determines whether tab is active. */
  $scope.isTabActive = function(index) {
    return $($('#class-nav > li')[index]).hasClass('active');
  }
  
  /* Opens a dropdown from the sidebar and doesn't apply clicks to parent. */
  $scope.toggleDropdown = function(group, event) {
    event.stopPropagation();
    $scope.openedDropdownGroup = ($scope.openedDropdownGroup == group) ? null : group;
    console.log("toggle dropdowns", group);
  };

  $scope.toggleClassInfo = function(group, event) {
    $scope.openedClassInfo = ($scope.openedClassInfo == group) ? null : group;
    console.log("toggleclass infos", group);
  };

  $scope.toggleChatSig = function(index) {
    $scope.openedChatSig = ($scope.openedChatSig == index) ? null : index;
  };

  $scope.closeSideNav = function() {
    console.log('Close the side nav');
    $('.button-collapse').sideNav('hide');
  };

  $scope.focusInput = function() {
    document.getElementById("input--message").focus();
  }

  $scope.tellMeStuff = function() {
    console.log("TELL ME STUFF");
    console.log("$scope.openedDropdownGroup=", $scope.openedDropdownGroup);
    console.log("$scope.openedClassInfo=", $scope.openedClassInfo);
  };

  /* Occurs before other things */
  $(document).ready(function() {
    // materializecss initialization
    $('.button-collapse').sideNav({
      menuWidth: 240, // Default is 240
      edge: 'left', // Choose the horizontal origin
      closeOnClick: true // Closes side-nav on <a> clicks, useful for Angular/Meteor
    });

    $('.tooltipped').tooltip({delay: 50});

    $('.dropdown-button').dropdown({
      inDuration: 300,
      outDuration: 225,
      constrain_width: false, // Doesn't change width of dropdown to that of the activator
      hover: false, // Activate on hover
      gutter: 0, // Spacing from edge
      belowOrigin: false // Displays dropdown below the button
    });

    $scope.focusInput();

    /* Whole document click listener where you can closing event functions */  
    $(document).click(function(event) {
      // Closes the opened .sp-dropdowns on any click outside of it.

/*
      console.log(event.target);


      if ($scope.openedChatSig != null &&
          event.target !== $('.chat-bubble--open-sig')) {
        $scope.openedChatSig = null;
        console.log('CLOSE THIS SIG');
        $scope.$apply();
      }
*/

      if ($scope.openedDropdownGroup != null &&
          event.target != $('.sp-dropdown--open')) {
        $scope.openedDropdownGroup = null;
        console.log('CLOSE THIS STUFF');
        $scope.$apply();
      }
    });
  });
}]).

filter('dateify_time', function() {
  return function(input) {
    //console.log('dateify time:', input);
    return moment(input).format('ddd, MM/DD/YYYY, h:mmA');
  };
}).

filter('dateify_date', function() {
  return function(input) {
    //console.log('dateify date:', input);
    return moment(input).format('ddd, MM/DD/YYYY');
  };
}).

filter('titler', function() {
  return function(input) {
    console.log('titler :', input);
    var currentTab = input.split('#/')[1]; 
    if (currentTab == 1) {
      currentTab = 0;
    }
    return $scope.rooms[currentTab];
  };
});
