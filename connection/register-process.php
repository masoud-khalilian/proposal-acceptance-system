<?php
include_once 'C:\xampp\htdocs\21-uni-project/inc/head.php';


$password=$_POST['password'];
$password_confirmation=$_POST['password_confirmation'];

if($password===$password_confirmation){
$student = new student();
$student->setUsername($_POST['username']);
$student->setPassword($_POST['password']);
$student->setFName($_POST['f_name']);
$student->setLName($_POST['l_name']);
$student->setFieldLevel($_POST['field_level']);
$student->setDb($con2);

$student->student_register();



}else{
    header('location:../index.php?er=register&&ind=register');
}