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
    database: 'ucsdspace'
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

  socket.on('leave group', function(msg) {
    if(("group_id" in msg) && ("user_id" in msg)) {
      console.log("Leaving group: " + msg['group_id']);
      leaveGroup(msg, socket);
    }
  });

  socket.on('edit group', function(msg) {
    if(("group_id" in msg) && ("long_desc" in msg) && ("group_name" in msg)) {
      console.log("Editing group: " + msg['group_id']);
      editGroup(msg, socket); 
    }
  });

});

function getUserInformation(uid, socket) {
  var sql = 'SELECT username FROM users WHERE hash=?;';
  sql = mysql.format(sql, uid);
  db.query(sql, function(err, rows, fields) {
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
  var sql = 'SELECT g.name group_name, IF(g.id IN (SELECT id from class_groups), 0, 1) is_study_group, g.hash group_id, sg.long_desc, sg.start_time, sg.end_time, gg.name class_name, (SELECT COUNT(*) FROM users_to_groups where group_id = g.id) group_size FROM users_to_groups ug JOIN groups g ON ug.group_id = g.id JOIN users u ON user_pid = u.pid LEFT JOIN study_groups sg on g.id = sg.id left join groups gg on gg.id = sg.class_id WHERE (sg.end_time > NOW() OR sg.id IS NULL) AND u.hash = ?;';
  sql = mysql.format(sql, uid);
  db.query(sql, function(err, rows, fields) {
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
  var sql = 'SELECT g.name group_name, g.hash group_id, m.message, CASE m.isAnonymous WHEN 0 THEN u.username ELSE \'Anonymous\' END username, m.time FROM messages m JOIN users_to_groups ug ON m.group_id = ug.group_id JOIN groups g ON ug.group_id = g.id JOIN users u ON m.user_pid = u.pid JOIN users u1 on ug.user_pid = u1.pid WHERE u1.hash = ?;';
  sql = mysql.format(sql, uid);  
  db.query(sql, function(err, rows, fields) {
  console.log(rows);
  socket.emit('new messages', rows);
  console.log("Sent class message information to user " + uid);
});

};

function storeMessage(msg) {
  var pid;
  var gid;
  var sql = 'SELECT pid FROM users WHERE hash = ?;';
  sql = mysql.format(sql, msg['user_id']);
  db.query(sql, function(err, rows, fields) {
    console.log(rows);
    if(rows.length == 1) {
      pid = rows[0]['pid'];
      sql = 'SELECT id FROM groups where hash = ?;';
      sql = mysql.format(sql, msg['group_id']);
      db.query(sql, function(err, rows, fields) {
        console.log(rows);
        if(rows.length == 1) {
          gid = rows[0]['id'];
          sql = 'INSERT INTO messages (group_id, user_pid, message, isAnonymous) VALUES (' + gid + ', "' + pid + '", ?, ?);';
          sql = mysql.format(sql, [msg['message'], msg['isAnonymous']]);
          db.query(sql, function(err, rows, fields) {
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

function leaveGroup(msg, socket) {
  db.beginTransaction(function(err) {
    if(err) {throw err;}
    var sql = 'DELETE ug FROM users u JOIN users_to_groups ug ON u.pid = ug.user_pid JOIN groups g ON ug.group_id = g.id WHERE u.hash = ? AND g.hash = ?;';
    sql = mysql.format(sql, [msg['user_id'], msg['group_id']]);
    console.log(sql);
    db.query(sql, function(err, rows, fields) {
      if(err) {
        db.rollback(function() {
          socket.emit('generic error');
          throw err;
        });
      }

      /* SQL to delete an empty study group. FK relationship will delete the group */
      var sql = 'DELETE g FROM groups g JOIN study_groups sg ON g.id = sg.id WHERE g.hash = ? AND NOT EXISTS (SELECT * FROM users_to_groups ug where ug.group_id = g.id);';
      sql = mysql.format(sql, [msg['group_id'], msg['group_id']]);
      console.log(sql);
      db.query(sql, function(err, rows, fields) {
        if(err) {
          db.rollback(function() {
            socket.emit('generic error');
            throw err;
          });
        }
        
        db.commit(function(err) {
          if(err) {
            db.rollback(function() {
              socket.emit('generic error');
              throw err;
            });
          }
          socket.emit('refresh');
        });
      });
    });
  });
};

//Give: long_desc, group_name, group_id
function editGroup(msg, socket) {
  var sql = 'UPDATE groups g, study_groups sg SET g.name = ?, sg.long_desc = ? WHERE g.id = sg.id AND g.hash = ?;';
  sql = mysql.format(sql, [msg['group_name'], msg['long_desc'], msg['group_id']]);  
  db.query(sql, function(err, rows, fields) {
    console.log("Edited group description!");
    socket.emit('refresh');
  });
};
