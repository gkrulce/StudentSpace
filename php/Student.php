<?php
class Student
{ 
  private $pid;
  private $name;
  private $hash;

  /* Constructor */
  public function __construct($pid, $name, $hash)
  {
    $this->pid = $pid;
    $this->name = $name;
    $this->hash = $hash;
  }

  /* Getter methods */
  public function getHash()
  {
    return $this->hash;
  }

  public function getName()
  {
    return $this->name;
  }
  public function getAllStudyGroups($db)
  {
    //Hardcoded value assumes PST
    return $db->query('SELECT (SELECT COUNT(*) FROM users_to_groups WHERE group_id = sg.id) group_size, g2.name AS group_name, g.name class_name, g2.hash group_id, sg.date date, t.name time, sg.long_desc FROM study_groups sg JOIN groups g ON sg.class_id = g.id JOIN users_to_groups usg ON usg.group_id = g.id JOIN groups g2 ON g2.id = sg.id JOIN study_group_times t ON sg.time = t.id WHERE usg.user_pid = \'' . $this->pid . '\' AND sg.id NOT IN (SELECT group_id FROM users_to_groups WHERE user_pid = \'' . $this->pid . '\') AND DATE_ADD(sg.date, INTERVAL t.offset HOUR) > DATE_SUB(NOW(), INTERVAL 7 HOUR);');
  }

  public function getClasses($db)
  {
    return $db->query('SELECT g.name class_name, g.hash AS class_id, ug.desires_email FROM users u JOIN users_to_groups ug ON u.pid = ug.user_pid JOIN groups g ON ug.group_id = g.id JOIN class_groups cg ON g.id = cg.id WHERE u.pid = \'' . $this->pid .'\';');
  }

  public function getCurrentStudyGroups($db)
  {
    return $db->query('SELECT (SELECT COUNT(*) FROM users_to_groups ug WHERE ug.group_id = g.id) AS group_size, g.hash group_id, c.name class_name, sg.start_time start_date_time, g.name group_name, sg.long_desc FROM users u JOIN users_to_groups ug ON u.pid = ug.user_pid JOIN groups g ON ug.group_id = g.id JOIN study_groups sg ON g.id = sg.id JOIN groups c ON c.id = sg.class_id WHERE u.pid = \'' . $this->pid . '\';');
  }

  /* Setter methods */

  /* $assocArray must have certain key-value pairs or database query will fail.
   */
  public function createStudyGroup($db, $arr)
  {
    include('purify.php');
    $arr['short_desc'] = $purifier->purify($arr['short_desc']);
    $arr['long_desc'] = $purifier->purify($arr['long_desc']);
    /* An example of how to set up an associate array for this function 
    $assocArray = array();
    $assocArray["start_time"] = "2015-01-01 12:00:00";
    $assocArray["short_desc"] = "TEST2";
    $assocArray["long_desc"] = "TESTEST2";
    $assocArray["class_id"] = 8; */

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    try {
      $db->beginTransaction();
      $sth = $db->prepare('INSERT INTO groups(name, hash) VALUES (:groupName, md5(rand()));');

      $sth->bindParam(':groupName', $arr['short_desc'], PDO::PARAM_STR);
      $sth->execute();

      $groupId = $db->lastInsertId();

      $assocArray['longDesc'] = $arr['long_desc'];
      $assocArray['classId'] = $this->getGroupIdByHash($db, $arr['class_id']);
      $assocArray['groupId'] = $groupId;
      $assocArray['date'] = $arr['date'];
      $assocArray['time'] = $arr['time'];

      $sth = $db->prepare('INSERT INTO study_groups (id, class_id, long_desc, date, time) VALUES (:groupId, :classId, :longDesc, :date, :time);');
      $sth->execute($assocArray);
      $groupHash = $this->getGroupHashById($db, $groupId);
      $result = $this->joinStudyGroup($db, $groupHash);
        
      $db->commit();
      $db->setAttribute(PDO::ATTR_ERRMODE, $GLOBALS['db_errmode']);
      return $result;
    }catch(Exception $e) {
      $db->rollback();
      $db->setAttribute(PDO::ATTR_ERRMODE, $GLOBALS['db_errmode']);
      return false;
    }
  }

  public function getGroupIdByHash($db, $groupHash)
  {
    $sth = $db->prepare('SELECT id FROM groups WHERE hash = :hash;');
    $sth->bindParam(':hash', $groupHash, PDO::PARAM_STR);

    $sth->execute();
    return $sth->fetch(PDO::FETCH_NUM)[0];
  }

  public function getGroupHashById ($db, $groupId) 
  {
    return $db->query('SELECT hash FROM groups WHERE id = ' . $groupId . ';')->fetch(PDO::FETCH_BOTH)[0];
  }

  public function exitStudyGroup($db, $groupHash)
  {
    $groupId = $this->getGroupIdByHash($db, $groupHash);

    $sth = $db->prepare('DELETE FROM users_to_groups WHERE group_id = :groupId AND user_pid = :userPID;');
    $sth->bindParam(':groupId', $groupId, PDO::PARAM_INT);
    $sth->bindParam(':userPID', $this->pid, PDO::PARAM_STR);

    return $sth->execute();
  }

  public function joinStudyGroup($db, $groupHash)
  {
    $groupId = $this->getGroupIdByHash($db, $groupHash);

    $sth = $db->prepare('INSERT INTO users_to_groups (user_pid, group_id) VALUES (:userPID, :groupId);');
    $sth->bindParam(':userPID', $this->pid, PDO::PARAM_STR);
    $sth->bindParam(':groupId', $groupId, PDO::PARAM_INT);

    return $sth->execute();
  }

  public function updateEmailPreferences($db, $arr) {
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    try {
      $db->beginTransaction();
      $db->query('UPDATE users_to_groups ug JOIN class_groups c ON ug.group_id = c.id SET ug.desires_email = 0 WHERE ug.user_pid = "' . $this->pid . '";');

      $holders = array();
      foreach($arr as $row) {
        $holders[] = "?";
      }

      $sth = $db->prepare('UPDATE users_to_groups ug JOIN groups g ON ug.group_id = g.id SET ug.desires_email = 1  where ug.user_pid = "' . $this->pid . '" AND g.hash IN (' . implode(', ', $holders) . ');');

      $result = $sth->execute($arr);

      $db->commit();
      $db->setAttribute(PDO::ATTR_ERRMODE, $GLOBALS['db_errmode']);
      return $result;
    } catch(Exception $e) {
      $db->rollback();
      $db->setAttribute(PDO::ATTR_ERRMODE, $GLOBALS['db_errmode']);
      return false;
    }
  }

}

/* Tester code */
  /*include('dbv2.php');
  $stud = new Student('A11541442', 'gkrulce');
  $assocArr = array();
  $assocArr["start_date_time"] = "2015-01-01 12:00:00";
  $assocArr["short_desc"] = "Test Group";
  $assocArr["long_desc"] = "This is a test group's agenda.";
  $assocArr["class_id"] = 1;
  $stud->createStudyGroup($GLOBALS['db'], $assocArr);
  $rach = new Student('A11111111', 'rlee');
  //$rach->exitStudyGroup($GLOBALS['db'], "22");
  foreach($stud->getCurrentStudyGroups($GLOBALS['db']) as $row)
  {
    var_dump($row);
  }
  foreach($rach->getCurrentStudyGroups($GLOBALS['db']) as $row)
  {
    var_dump($row);
  }

  foreach($stud->getAllStudyGroups($GLOBALS['db']) as $row) {
    var_dump($row);
  }*/
  
?>
