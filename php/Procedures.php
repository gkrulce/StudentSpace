<?php
  class Procedures {
    public static function login($db, $pid) {
      $stmt = $db->prepare("SELECT * FROM users where pid = :pid;");
      $stmt->bindParam(":pid", $pid, PDO::PARAM_STR);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function logLogin($db, $pid) {
        $stmt = $db->prepare("INSERT INTO logins (user_pid) VALUES (:pid);");
	$stmt->bindParam(":pid", $pid, PDO::PARAM_STR);
	$stmt->execute();
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

    public static function register($db, $server) {
      $pid = $server['PID'];
      $firstName = $server['FIRST_NAME'];
      $lastName = $server['LAST_NAME'];
      $username = $server['NETWORKUSERID'];
      $email = $server['LONG_EMAIL'];

      $stmt=$db->prepare('INSERT INTO users (pid, email, first_name, last_name, username, hash) VALUES (:pid, :email, :first_name, :last_name, :username, :hash);');
      $stmt->bindParam(":pid", $pid);
      $stmt->bindParam(":email", $email);
      $stmt->bindParam(":first_name", $firstName);
      $stmt->bindParam(":last_name", $lastName);
      $stmt->bindParam(":username", $username);
      $stmt->bindParam(":hash", md5(rand()));

      $stmt->execute();

    }
  }
?>
