<?php
  class Procedures {
    public static function login($db, $username) {
      $stmt = $db->prepare("SELECT * FROM users where username = :userName;");
      $stmt->bindParam(":userName", $username, PDO::PARAM_STR);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getStudyTimes($db) {
      return $db->query('SELECT id, name, time_range FROM study_group_times;');
    }

    public static function getOffset($db, $id) {
      $stmt = $db->prepare('SELECT offset FROM study_group_times WHERE id = :id;');
      $stmt->bindParam(":id", $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_NUM)[0];
    }
  }
?>
