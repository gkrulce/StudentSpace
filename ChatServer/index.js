/* SERVER SIDE Javascript */

// Setup basic express server
var express = require('express');
var app = express();
var server = require('http').createServer(app);
var io = require('socket.io')(server);
var port = process.env.PORT || 3000;
var mysql = require('mysql');
var db = mysql.createConnection({
  host: 'localhost',
  user: 'webapp',
  password: '',
  database: 'StudyTree'
});

server.listen(port, function () {
  console.log('Server listening at port %d', port);
});

// Routing
app.use(express.static(__dirname + '/public'));

io.on('connection', function(socket){

  socket.on('login', function(msg) {
    //Verify no SQL injection
    console.log("User " + msg + " logged in!");
    getUserInformation(msg);
    getChatRooms(msg);
    getMessages(msg);
});
});

function getUserInformation(uid) {
  //VERIFY NO SQL INJECTION
  db.query('Select username from users WHERE pid="' + uid + '";', function(err, rows, fields) {
    if(rows != rows.length == 1)
    {
      var row = rows[0];
      //console.log(row);
      io.emit('user information', row);
      console.log("Sent user information to user " + uid);
    }
  });
};

function getChatRooms(uid) {
  db.query('SELECT g.name group_name, g.hash group_id FROM users_to_groups ug JOIN groups g ON ug.group_id = g.id WHERE ug.user_pid = "' + uid + '";', function(err, rows, fields) {
    console.log(rows);
    io.emit('add rooms', rows);
    console.log("Sent class information to user " + uid);
});
};

function getMessages(uid) {
    db.query('SELECT g.name group_name, g.hash group_id, m.message, CASE m.isAnonymous WHEN 0 THEN u.username ELSE \'Anonymous\' END username, m.time FROM messages m JOIN users_to_groups ug ON m.group_id = ug.group_id JOIN groups g ON ug.group_id = g.id JOIN users u ON ug.user_pid = u.pid WHERE ug.user_pid = "' + uid + '";', function(err, rows, fields) {
    console.log(rows);
    io.emit('new messages', rows);
    console.log("Sent class message information to user " + uid);
});
};
