<?php
//TODO include session.php perhaps or maybe just pass in a student object
class StudyGroup
{
  protected $groupId;
  protected $groupSize;
  protected $dateTime;
  protected $shortDesc;
  protected $longDesc;
  protected $classId;

  /* Constructor used to create a new study group.
     Input parameters should have already been cleaned for malicious content */
  public function __construct($assocArray)
  {
    $this->dateTime = $assocArray['start_time'];
    $this->shortDesc = $assocArray['short_desc'];
    $this->longDesc = $assocArray['long_desc'];
    $this->classId = $assocArray['id'];
    $this->groupSize = $assocArray['group_size'];
    $this->groupId = $assocArray['group_id'];
  }

  public function createGroup($db, $studentId)
  {
    $sth = $db->prepare('INSERT INTO study_groups (class_id, start_time, short_desc, long_desc) VALUES (:classId, :startTime, :shortDesc, :longDesc);');
    $sth->bindParam(':classId', $classId, PDO::PARAM_INT);
    $sth->bindParam(':startTime', $dateTime, PDO::PARAM_STR);
    $sth->bindParam(':shortDesc', $shortDesc, PDO::PARAM_STR);
    $sth->bindParam(':longDesc', $longDesc, PDO::PARAM_STR);

    $sth->execute();
    $this->joinGroup($db, $studentId);
  }

  /* The only field populated is $groupId */
  public function exitGroup($db, $studentId)
  {
    $sth = $db->prepare('DELETE FROM users_to_study_groups WHERE study_group_id = :groupId AND user_id = :userId;');

    $sth->bindParam(':groupId', $groupId, PDO::PARAM_INT);
    $sth->bindParam(':userId', $studentId, PDO::PARAM_INT);

    $sth->execute();
  }


  /* The only field populated is $groupId */
  public function joinGroup($db, $studentId)
  {
    $hash = md5(uniqid());
    $sth = $db->prepare('INSERT INTO users_to_study_groups (study_group_id, user_id, hash) VALUES (:groupId, :userId, :hash);');
    $sth->bindParam(':groupId', $groupId, PDO::PARAM_INT);
    $sth->bindParam(':userId', $studentId, PDO::PARAM_INT);
    $sth->bindParam(':hash', $hash, PDO::PARAM_STR);

    $sth->execute();
  }
}
?>
