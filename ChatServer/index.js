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
    getClassInformation(msg);
    getStudyGroupInformation(msg);
    getClassMessages(msg);
    getStudyGroupMessages(msg);
});

  socket.on('special', function(msg) {
    db.query('insert into messages (user, groupId, content) VALUES (1, md5(rand()), \'RANDOM TEXTLALALALA\');');
  });

});

function getUserInformation(uid) {
  db.query('Select user_name from users WHERE id=' + uid + ';', function(err, rows, fields) {
    var row = rows[0];
    //console.log(row);
    io.emit('user information', row);
    console.log("Sent user information to user " + uid);
  });
};

function getClassInformation(uid) {
  db.query('SELECT c.name, c.uuid FROM users u join users_to_classes uc on u.id = uc.user_id join v_class c on uc.class_id = c.id where u.id = ' + uid + ';', function(err, rows, fields) {
    //console.log(rows);
    io.emit('add rooms', rows);
    console.log("Sent class information to user " + uid);
});
};

function getStudyGroupInformation(uid) {
    db.query('select sg.short_desc name, sg.uuid from users u join users_to_study_groups usg on u.id = usg.user_id join study_groups sg on usg.study_group_id = sg.id where u.id=' + uid + ';', function(err, rows, fields) {
    //console.log(rows);
    io.emit('add rooms', rows);
    console.log("Sent study group information to user " + uid);
});
};

function getClassMessages(uid) {
    db.query('SELECT c.name, c.uuid, m.content, CASE m.isAnonymous WHEN 0 THEN u2.user_name ELSE \'Anonymous\' END as user_name, m.time FROM users u1 join users_to_classes uc on uc.user_id = u1.id join v_class c on uc.class_id = c.id join messages m on m.group_uuid = c.uuid join users u2 on m.user = u2.id where u1.id = ' + uid + ' ORDER BY m.time;',function(err, rows, fields) {
    io.emit('new messages', rows);
    console.log("Sent class message information to user " + uid);
});
};


function getStudyGroupMessages(uid) {
    db.query('SELECT sg.short_desc name, sg.uuid, m.content, CASE m.isAnonymous WHEN 0 THEN u2.user_name ELSE \'Anonymous\' END as user_name, m.time FROM users u1 join users_to_study_groups usg on usg.user_id = u1.id join study_groups sg on usg.study_group_id = sg.id join messages m on m.group_uuid = sg.uuid join users u2 on m.user = u2.id where u1.id = ' + uid + ' ORDER BY m.time;', function(err, rows, fields) {
    io.emit('new messages', rows);
    console.log("Sent study group message information to user " + uid);
});
};
