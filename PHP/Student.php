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
    return $db->query('SELECT c.name, sg.start_time, sg.end_time, sg.id group_id FROM study_groups sg JOIN v_class c ON sg.class_id = c.id JOIN users_to_classes uc on c.id = uc.class_id JOIN users u ON u.id = uc.user_id WHERE u.id = ' . $this->uid . ' AND sg.id NOT IN (SELECT study_group_id FROM users_to_study_groups WHERE user_id = ' . $this->uid . ');');
  }

  public function getClasses($db)
  {
    return $db->query('SELECT c.id, c.name FROM users u JOIN users_to_classes uc ON u.id=uc.user_id JOIN v_class c ON c.id=uc.class_id WHERE u.id='. $this->uid.';');
  }

  public function getCurrentStudyGroups($db)
  {
    return $db->query('SELECT c.id, c.name, sg.start_time, sg.end_time, sg.id group_id FROM users u JOIN users_to_study_groups usg ON u.id = usg.user_id JOIN study_groups sg on usg.study_group_id = sg.id JOIN v_class c ON c.id = sg.class_id WHERE u.id=' . $this->uid . ';');
  }

  /* Setter methods */
  public function createStudyGroup($db)
  {

  }

  public function exitStudyGroup($db)
  {

  }

  public function joinStudyGroup($db, $groupId)
  {
    
  }

}
  include('db.php');
  $stud = new Student(2, 'gkrulce');
  foreach($stud->getAllStudyGroups($db) as $row)
  {
    var_dump($row);
  }
?>
