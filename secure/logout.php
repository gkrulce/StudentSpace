<?php
session_start();
if(session_destroy())
{
  header("Location: /Shibboleth.sso/Logout?return=https://a4.ucsd.edu/tritON/logout?target=https://studygroups.ucsd.edu"); // Redirect home page
}
?>
