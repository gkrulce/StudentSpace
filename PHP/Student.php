<?php
include('StudyGroup.php');
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
    $groups = array();
    $result = $db->query('SELECT (SELECT COUNT(*) FROM users_to_study_groups usg where usg.study_group_id = sg.id) group_size, c.id, c.name, sg.start_time, sg.id group_id, sg.short_desc, sg.long_desc FROM study_groups sg JOIN v_class c ON sg.class_id = c.id JOIN users_to_classes uc on c.id = uc.class_id JOIN users u ON u.id = uc.user_id WHERE u.id = ' . $this->uid . ' AND sg.id NOT IN (SELECT study_group_id FROM users_to_study_groups WHERE user_id = ' . $this->uid . ');');

    foreach($result as $row)
    {
      $groups[] = new StudyGroup($row);
    }
    return $groups;
  }

  public function getClasses($db)
  {
    return $db->query('SELECT c.id, c.name FROM users u JOIN users_to_classes uc ON u.id=uc.user_id JOIN v_class c ON c.id=uc.class_id WHERE u.id='. $this->uid.';');
  }

  public function getCurrentStudyGroups($db)
  {
    $groups = array();
    $result = $db->query('SELECT (SELECT COUNT(*) FROM users_to_study_groups usg where usg.study_group_id = sg.id) group_size, c.id, c.name, sg.start_time, sg.id group_id, sg.short_desc, sg.long_desc FROM users u JOIN users_to_study_groups usg ON u.id = usg.user_id JOIN study_groups sg on usg.study_group_id = sg.id JOIN v_class c ON c.id = sg.class_id WHERE u.id=' . $this->uid . ';');
    foreach($result as $row)
    {
      $groups[] = new StudyGroup($row);
    }

    return $groups;
  }

  /* Setter methods */

  /* $assocArray must have certain key-value pairs or database query will fail.
     Refer to StudyGroup constructor for specifics. */
  public function createStudyGroup($db, $assocArray)
  {
    $newGroup = new StudyGroup($assocArray);
    $newGroup->createGroup($db, $this->uid);
  }

  public function exitStudyGroup($db, $groupId)
  {
    $assocArray = array();
    $assocArray['groupId'] = $groupId;
    $group = new StudyGroup($assocArray);
    $group->exitGroup($db, $this->uid);
  }

  public function joinStudyGroup($db, $groupId)
  {
    $assocArray = array();
    $assocArray['groupId'] = $groupId;
    $group = new StudyGroup($assocArray);
    $group->joinGroup($db, $this->uid);
  }

}

/* Tester code */
  include('db.php');
  $stud = new Student(2, 'gkrulce');
  $stud->createStudyGroup($db, $assocArray);
$result = $stud->getAllStudyGroups($db);
  foreach($result as $row)
  {
    var_dump($row);
  }
?>
