/* CLIENT SIDE Javascript */
var socket = io();
socket.on('connect', function() {
  console.log('User connecting');
  login();
});

var rooms;
socket.on('user information', function(msg) {
  console.log(msg);
});

socket.on('add rooms', function(msg) {
  rooms = msg;
});

socket.on('new message', function(msg) {
  console.log("new messages");
  console.log(msg);
});

function test() {
  console.log('Test');
  socket.emit('special', 'String');
};

/* The userid is passed as the id GET parameter */
function login() {
  var id = getParameterByName('id');
  console.log("Logging in with id: " + id);
  socket.emit('login', id);
};

/* Grabs the GET parameters */
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
};

/* ANGULAR JS */

var app = angular.module('Chat', []);

app.controller('ChatCtrl', ['$scope', function($scope) {
  $scope.rooms = [];
  $scope.roomDict = [];

  socket.on('add rooms', function(msg) {
    for(var i = 0 ; i < msg.length ; i++) {
      msg[i]['messages'] = [];
    }
    $scope.rooms = $scope.rooms.concat(msg);
    $scope.$apply();
    for(var i = 0 ; i < msg.length ; i++) {
      $scope.roomDict[msg[i]['uuid'] = i];
    }
//IF SOMETHING IS NOT ALREADY ACTIVE
    $('.gold-pills li:first').addClass('active');
    $('.tab-pane:first').addClass('active');
  });

  socket.on('new messages', function(msg) {
    for(var i = 0 ; i < msg.length ; i++) {
      $scope.rooms[roomDict[msg[i]['uuid']]]['messages'].concat(msg);
    }
    $scope.$apply();
  });

}]);
