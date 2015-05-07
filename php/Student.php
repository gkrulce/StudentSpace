<?php
include('db.php');
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
  public function getAllStudyGroups()
  {
    return $GLOBALS['db']->query('SELECT (SELECT COUNT(*) FROM users_to_groups WHERE group_id = sg.id) group_size, g2.name AS group_name, g.name class_name, g2.hash group_id, sg.start_time start_date_time, sg.long_desc FROM study_groups sg JOIN groups g ON sg.class_id = g.id JOIN users_to_groups usg ON usg.group_id = g.id JOIN groups g2 ON g2.id = sg.id WHERE usg.user_pid = \'' . $this->pid . '\' AND sg.id NOT IN (SELECT group_id FROM users_to_groups WHERE user_pid = \'' . $this->pid . '\');');
  }

  public function getClasses()
  {
    return $GLOBALS['db']->query('SELECT g.name class_name, g.hash AS class_id, ug.desires_email FROM users u JOIN users_to_groups ug ON u.pid = ug.user_pid JOIN groups g ON ug.group_id = g.id JOIN class_groups cg ON g.id = cg.id WHERE u.pid = \'' . $this->pid .'\';');
  }

  public function getCurrentStudyGroups()
  {
    return $GLOBALS['db']->query('SELECT (SELECT COUNT(*) FROM users_to_groups ug WHERE ug.group_id = g.id) AS group_size, g.hash group_id, c.name class_name, sg.start_time start_date_time, g.name group_name, sg.long_desc FROM users u JOIN users_to_groups ug ON u.pid = ug.user_pid JOIN groups g ON ug.group_id = g.id JOIN study_groups sg ON g.id = sg.id JOIN groups c ON c.id = sg.class_id WHERE u.pid = \'' . $this->pid . '\';');
  }

  /* Setter methods */

  /* $assocArray must have certain key-value pairs or database query will fail.
   */
  public function createStudyGroup($arr)
  {
    /* An example of how to set up an associate array for this function 
    $assocArray = array();
    $assocArray["start_time"] = "2015-01-01 12:00:00";
    $assocArray["short_desc"] = "TEST2";
    $assocArray["long_desc"] = "TESTEST2";
    $assocArray["class_id"] = 8; */

    $sth = $GLOBALS['db']->prepare('INSERT INTO groups(name, hash) VALUES (:groupName, md5(rand()));');

    $sth->bindParam(':groupName', $arr['short_desc'], PDO::PARAM_STR);
    $result = $sth->execute();


    if(!$result) {
      return $result;
    }

    $groupId = $GLOBALS['db']->lastInsertId();

    $assocArray['startTime'] = $arr['date'] . ' ' . $arr['start_time'];
    $assocArray['longDesc'] = $arr['long_desc'];
    $assocArray['classId'] = $this->getGroupIdByHash($arr['class_id']);
    $assocArray['groupId'] = $groupId;

    $sth = $GLOBALS['db']->prepare('INSERT INTO study_groups (id, class_id, long_desc, start_time) VALUES (:groupId, :classId, :longDesc, :startTime);');
    $result = $sth->execute($assocArray);

    if(!$result) {
      return $result;
    }

    $groupHash = $this->getGroupHashById($groupId);
    $result = $this->joinStudyGroup($groupHash);
      
    return $result;
  }

  public function getGroupIdByHash($groupHash)
  {
    $sth = $GLOBALS['db']->prepare('SELECT id FROM groups WHERE hash = :hash;');
    $sth->bindParam(':hash', $groupHash, PDO::PARAM_STR);

    $sth->execute();
    return $sth->fetch(PDO::FETCH_NUM)[0];
  }

  public function getGroupHashById ($groupId)
  {
    return $GLOBALS['db']->query('SELECT hash FROM groups WHERE id = ' . $groupId . ';')->fetch(PDO::FETCH_BOTH)[0];
  }

  public function exitStudyGroup($groupHash)
  {

    $groupId = $this->getGroupIdByHash($groupHash);

    $sth = $GLOBALS['db']->prepare('DELETE FROM users_to_groups WHERE group_id = :groupId AND user_pid = :userPID;');
    $sth->bindParam(':groupId', $groupId, PDO::PARAM_INT);
    $sth->bindParam(':userPID', $this->pid, PDO::PARAM_STR);

    return $sth->execute();
  }

  public function joinStudyGroup($groupHash)
  {

    $groupId = $this->getGroupIdByHash($groupHash);

    $sth = $GLOBALS['db']->prepare('INSERT INTO users_to_groups (user_pid, group_id) VALUES (:userPID, :groupId);');
    $sth->bindParam(':userPID', $this->pid, PDO::PARAM_STR);
    $sth->bindParam(':groupId', $groupId, PDO::PARAM_INT);

    return $sth->execute();
  }

  public function updateEmailPreferences($arr) {
    $db = new PDO($GLOBALS['dbn'], $GLOBALS['dbusr'], $GLOBALS['dbpass']);
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
      return $result;
    } catch(Exception $e) {
      $db->rollback();
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
