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
    getUserInformation(msg, socket);
    getChatRooms(msg, socket);
    getMessages(msg, socket);
});

  socket.on('new message', function(msg) {
    console.log("Received a message... sending!");
    console.log(msg);
    storeMessage(msg);
    delete msg['user_id']
//Maybe add the time here
    msg = [msg];
    console.log("Sending message to group: " + msg['group_id']);
    io.to(msg['group_id']).emit('new messages', msg);
});

});

function getUserInformation(uid, socket) {
  //VERIFY NO SQL INJECTION
  db.query('Select username from users WHERE hash="' + uid + '";', function(err, rows, fields) {
    if(rows != rows.length == 1)
    {
      var row = rows[0];
      console.log(row);
      socket.emit('user information', row["username"]);
      console.log("Sent user information to user " + uid);
    }
  });
};

function getChatRooms(uid, socket) {
  db.query('SELECT g.name group_name, g.hash group_id FROM users_to_groups ug JOIN groups g ON ug.group_id = g.id JOIN users u ON user_pid = u.pid WHERE u.hash = "' + uid + '";', function(err, rows, fields) {
    console.log(rows);
    socket.emit('add rooms', rows);
    console.log("Sent class information to user " + uid + ". Now joining rooms.");
    for(var i = 0 ; i < rows.length ; i++) {
      socket.join(rows['group_id']);
    }
});
};

function getMessages(uid, socket) {
    db.query('SELECT g.name group_name, g.hash group_id, m.message, CASE m.isAnonymous WHEN 0 THEN u.username ELSE \'Anonymous\' END username, m.time FROM messages m JOIN users_to_groups ug ON m.group_id = ug.group_id JOIN groups g ON ug.group_id = g.id JOIN users u ON ug.user_pid = u.pid JOIN users u1 on ug.user_pid = u1.pid WHERE u1.hash = "' + uid + '";', function(err, rows, fields) {
    console.log(rows);
    socket.emit('new messages', rows);
    console.log("Sent class message information to user " + uid);
});

};

function storeMessage(msg) {
  var pid;
  var gid;
  db.query('SELECT pid FROM users WHERE hash = "' + msg["user_id"] + '";', function(err, rows, fields) {
    console.log(rows);
    if(rows.length == 1) {
      pid = rows[0]['pid'];
      db.query('SELECT id FROM groups where hash = "' + msg["group_id"] + '";', function(err, rows, fields) {
        console.log(rows);
        if(rows.length == 1) {
          gid = rows[0]['id'];
          db.query('INSERT INTO messages (group_id, user_pid, message, isAnonymous) VALUES (' + gid + ', "' + pid + '", "' + msg['message'] + '", ' + msg['isAnonymous'] + ');', function(err, rows, fields) {
            console.log(rows);
            if(!err)
            {
              console.log("Inserted message successfully");
            }
          });
        }
      });
    }
  });
};
