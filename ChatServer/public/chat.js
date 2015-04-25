/* BUGSS
  query should return if we're in those classes not if it was sent by us.
  Messages not getting added to scope variable correctly
*/

/* CLIENT SIDE Javascript */
var socket = io();

socket.on('user information', function(msg) {
  console.log(msg);
});

function test() {
  console.log('Test');
  socket.emit('special', 'String');
};


/* ANGULAR JS */

var app = angular.module('Chat', []);

app.controller('ChatCtrl', ['$scope', function($scope) {

  $scope.rooms = [];
  $scope.roomDict = [];
  $scope.id = getParameterByName('id');
  $scope.username;
  $scope.isAnonymous = false;

  socket.on('connect', function() {
    console.log("Logging in with id: " + $scope.id);
    socket.emit('login', $scope.id);
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
      $scope.roomDict[$scope.rooms[i]['group_id']] = i;
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
      var pos = $scope.roomDict[msg[i]['group_id']];
      $scope.rooms[pos]['messages'] = $scope.rooms[pos]['messages'].concat(msg[i]);
      //$scope.rooms[$scope.roomDict[msg[i]['uuid']]]['messages'].concat(msg);
    }
    $scope.$apply();
  });

  socket.on('disconnect', function() {
    $scope.rooms = [];
    $scope.roomDict = [];
  });

  /* Grabs the GET parameters */
  function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
  };

}]);
