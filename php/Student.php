<?php
class Student
{ 
  private $uid;
  private $user_name;

  /* Constructor */
  public function __construct($uid, $user_name)
  {
    $this->uid = $uid;
    $this->user_name = $user_name;
  }

  /* Getter methods */
  public function getAllStudyGroups($db)
  {
    return $db->query('SELECT (SELECT COUNT(*) FROM users_to_study_groups usg where usg.study_group_id = sg.id) group_size, c.id, c.name, sg.start_time, sg.id group_id, sg.short_desc, sg.long_desc FROM study_groups sg JOIN v_class c ON sg.class_id = c.id JOIN users_to_classes uc on c.id = uc.class_id JOIN users u ON u.id = uc.user_id WHERE u.id = ' . $this->uid . ' AND sg.id NOT IN (SELECT study_group_id FROM users_to_study_groups WHERE user_id = ' . $this->uid . ');');
  }

  public function getClasses($db)
  {
    return $db->query('SELECT c.id, c.name FROM users u JOIN users_to_classes uc ON u.id=uc.user_id JOIN v_class c ON c.id=uc.class_id WHERE u.id='. $this->uid.';');
  }

  public function getCurrentStudyGroups($db)
  {
    return $db->query('SELECT (SELECT COUNT(*) FROM users_to_study_groups usg where usg.study_group_id = sg.id) group_size, c.id, c.name, sg.start_time, sg.id group_id, sg.short_desc, sg.long_desc FROM users u JOIN users_to_study_groups usg ON u.id = usg.user_id JOIN study_groups sg on usg.study_group_id = sg.id JOIN v_class c ON c.id = sg.class_id WHERE u.id=' . $this->uid . ';');
  }

  /* Setter methods */

  /* $assocArray must have certain key-value pairs or database query will fail.
   */
  public function createStudyGroup($db, $assocArray)
  {
    /* An example of how to set up an associate array for this function 
    $assocArray = array();
    $assocArray["start_date_time"] = "2015-01-01 12:00:00";
    $assocArray["short_desc"] = "TEST2";
    $assocArray["long_desc"] = "TESTEST2";
    $assocArray["class_id"] = 8; */

    $sth = $db->prepare('INSERT INTO study_groups (class_id, start_time, short_desc, long_desc, uuid) VALUES (:class_id, :start_date_time, :short_desc, :long_desc, UUID());');
    $result = $sth->execute($assocArray);
    if(!$result)
    {
      return $result;
    }
    $groupId = $db->lastInsertId();
    $result = $this->joinStudyGroup($db, $groupId);

    return $result;
  }

  public function exitStudyGroup($db, $groupId)
  {
    $sth = $db->prepare('DELETE FROM users_to_study_groups WHERE study_group_id = :groupId AND user_id = :userId;');

    $sth->bindParam(':groupId', $groupId, PDO::PARAM_INT);
    $sth->bindParam(':userId', $this->uid, PDO::PARAM_INT);

    $sth->execute();
  }

  public function joinStudyGroup($db, $groupId)
  {
    $sth = $db->prepare('INSERT INTO users_to_study_groups (study_group_id, user_id) VALUES (:groupId, :userId);');
    $sth->bindParam(':groupId', $groupId, PDO::PARAM_INT);
    $sth->bindParam(':userId', $this->uid, PDO::PARAM_INT);

    return $sth->execute();
  }

}

/* Tester code */
/*  include('db.php');
  $stud = new Student(2, 'gkrulce');
  $assocArr = array();
  $assocArr["start_date_time"] = "2015-01-01 12:00:00";
  $assocArr["short_desc"] = "TEST2";
  $assocArr["long_desc"] = "TESTEST2";
  $assocArr["class_id"] = 8;
  $stud->createStudyGroup($db, $assocArr);
  //$stud->joinStudyGroup($db, "71");
  foreach($stud->getCurrentStudyGroups($db) as $row)
  {
    //var_dump($row);
  } */
?>
