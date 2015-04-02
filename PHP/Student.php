<?php
class Student
{ 
  private $uid;
  private $user_name;

  public function __construct($uid, $user_name)
  {
    $this->uid = $uid;
    $this->user_name = $user_name;
  }

  public function getClasses($db)
  {
    return $db->query('SELECT c.id, c.name FROM users u JOIN users_to_classes uc ON u.id=uc.user_id JOIN v_class c ON c.id=uc.class_id WHERE u.id='. $this->uid.';');
  }
}
?>
