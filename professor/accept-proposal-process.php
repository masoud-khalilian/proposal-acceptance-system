<?php
include_once '../inc/head.php';

$proposal=new proposal();
$proposal->setDb($con2);
$proposal->insert_professor_to_proposal($_GET['proposal'],$_SESSION['user_id']);

header('location:../index.php');

