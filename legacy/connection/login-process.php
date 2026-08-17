<?php
include_once 'C:\xampp\htdocs\21-uni-project/inc/head.php';

$username = $_POST['username'];
$password = $_POST['password'];
$role = $_POST['role'];
switch ($_POST['role']) {
    case 'student':
        $student = new student();
        $student->setUsername($username);
        $student->setPassword($password);
        $student->setRole($role);
        $student->setDb($con2);
        $student->student_login();
        break;
    case 'professor':
        $professor = new professor();
        $professor->setUsername($username);
        $professor->setPassword($password);
        $professor->setRole($role);
        $professor->setDb($con2);
        $professor->professor_login();

        break;

    case 'admin':
        $admin = new adminClass();
        $admin->setUsername($username);
        $admin->setPassword($password);
        $admin->setRole($role);
        $admin->setDb($con);
        $admin->member_login();
        break;


}


if(isset($_SESSION['user_session'])){

    header('location: ../index.php');

}else{
    header('location: ../index.php?er=login');
}
