<?php
include_once '../inc/head.php';
$professor_ch = $_POST['professor_ch'];
$student = new student();
$student->setUsername($_POST['username']);
$student->setDb($con2);
$title = $_POST['title'];
$content = $_POST['content'];
$date_creation = mktime();
$student->send_proposal($title, $content, $date_creation,$professor_ch);
header('location: ../index.php');