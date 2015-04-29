/* SERVER SIDE Javascript */

// Setup basic express server
var express = require('express');
var app = express();
var server = require('http').createServer(app);
var io = require('socket.io')(server);
var port = process.env.PORT || 3000;
var mysql = require('mysql');
var db;
var db_config = {
  host: 'localhost',
    user: 'webapp',
    password: '',
    database: 'StudyTree'
};

function handleDisconnect() {
  db = mysql.createConnection(db_config); // Recreate the connection, since
                                                  // the old one cannot be reused.

  db.connect(function(err) {              // The server is either down
    if(err) {                                     // or restarting (takes a while sometimes).
      console.log('error when connecting to db:', err);
      setTimeout(handleDisconnect, 2000); // We introduce a delay before attempting to reconnect,
    }                                     // to avoid a hot loop, and to allow our node script to
  });                                     // process asynchronous requests in the meantime.
                                          // If you're also serving http, display a 503 error.
  db.on('error', function(err) {
    console.log('db error', err);
    if(err.code === 'PROTOCOL_CONNECTION_LOST') { // Connection to the MySQL server is usually
      handleDisconnect();                         // lost due to either server restart, or a
    } else {                                      // connnection idle timeout (the wait_timeout
      throw err;                                  // server variable configures this)
    }
  });
}

handleDisconnect();

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
    //console.log(msg);
    storeMessage(msg);
    delete msg['user_id']
//Maybe add the time here
    var group_id = msg['group_id'];
    console.log("Sending message to group: " + group_id);
    msg = [msg];

    console.log("WE ARE SENDING THIS INFORMATION. NOTHING CHANGES");
    console.log(msg);

    io.to(group_id).emit('new messages', msg);
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
      //console.log(rows[i]['group_id']);
      socket.join(rows[i]['group_id']);
    }
});
};

function getMessages(uid, socket) {
    db.query('SELECT g.name group_name, g.hash group_id, m.message, CASE m.isAnonymous WHEN 0 THEN u.username ELSE \'Anonymous\' END username, m.time FROM messages m JOIN users_to_groups ug ON m.group_id = ug.group_id JOIN groups g ON ug.group_id = g.id JOIN users u ON m.user_pid = u.pid JOIN users u1 on ug.user_pid = u1.pid WHERE u1.hash = "' + uid + '";', function(err, rows, fields) {
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
