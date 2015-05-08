<?php
include('session.php');
if(isset($_POST['id'])) {
  if($_SESSION['user']->joinStudyGroup($db, $_POST['id'])) {
    echo $_POST['id'];
  }else
  {
    http_response_code(500);
  }
}else
{
  http_response_code(400);
}
?>
