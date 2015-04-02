<?php
include('Section.php');
class Student
{ 
  private $uid;
  private $user_name;
  private $db;
  private $classes = array();

  public function __construct($uid, $db)
  {
    $this->db = $db;
    $this->uid = $uid;

    //Fetches classes into $classes array
    $result = $this->db->query('SELECT c.id, c.name FROM users u JOIN users_to_classes uc ON u.id=uc.user_id JOIN v_class c ON c.id=uc.class_id WHERE u.id='. $this->uid.';');
    
    foreach($result as $row)
    {
      $this->classes[] = new Section($row["id"], $row["name"]);
    }
  }

  public function getClasses()
  {
    return $this->classes;
  }
}

include('db.php');
$stud = new Student(2, $db);
foreach($stud->getClasses() as $row)
{
  echo $row->getName() . "\n";
}
?>
