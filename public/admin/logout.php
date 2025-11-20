<?php
session_start();
unset($_SESSION['admin_authenticated']);
unset($_SESSION['admin_login_time']);
session_destroy();
header('Location: login.php');
exit;
?>