<?php

include_once 'C:\xampp\htdocs\21-uni-project/inc/head.php';
echo $_SESSION['user_session'];

$log_out=$_POST['log'];
if(isset($log_out)){
$student = new student();
$student->logout();
}
?>
<a rel="stylesheet" href="../index.php"> index</a>

