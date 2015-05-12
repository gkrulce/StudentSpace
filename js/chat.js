function chatReset() {
  $(".resetOnSubmit").val("");
}
var socket = io(":3000");

/* ANGULAR JS */

var app = angular.module('ChatApp', []);

app.controller('ChatCtrl', ['$scope', function($scope) {

  $scope.rooms = [];
  $scope.id = userId;
  $scope.username = "";
  $scope.isAnonymous = false;


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

  $scope.sendMessage = function() {
    var loc;
    $('.gold-pills > li').each(function(index) {
      if($(this).hasClass('active')) {
        loc = index;
      }
    });

    if(typeof loc == 'undefined') {
      console.log("Can't send a message if no chat rooms are active!!!");
    }else
    {
    
      var message = {"group_name": $scope.rooms[loc]["group_name"], "group_id": $scope.rooms[loc]["group_id"], "message": $scope.inputText, "username": $scope.username, "user_id": $scope.id, "isAnonymous": $scope.isAnonymous};
      if($scope.isAnonymous) {
        message["username"] = "Anonymous";
      }
      console.log("Sending message...");
      console.log( message);
      socket.emit('new message', message);
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

    /* Sets the first pill to active and shows its content if all inactive  */
    if(!$('.gold-pills .active').length) {
      $('.gold-pills li:first').addClass('active');
      $('.tab-pane:first').addClass('active');
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
    $('#message-pane').scrollTop($('#message-pane')[0].scrollHeight);
    console.log("should have scrolled noob");
  });

  socket.on('disconnect', function() {
    $scope.rooms = [];
  });

  socket.on('refresh', function() {
    location.reload();
  });

  $scope.scrollDownChat = function(tab) {
    console.log("scroll down chat...");
    console.log(tab);
    $('#message-pane').scrollTop($('#message-pane')[0].scrollHeight);
    console.log("scroll down chat DONE");
  };

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

}]);
