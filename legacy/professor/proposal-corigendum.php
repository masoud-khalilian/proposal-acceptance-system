<?php
include_once '../inc/head.php';
$proposal=new proposal();
$proposal->setId($_POST['proposal_id']);
$proposal->setContent($_POST['content']);
$proposal->setDb($con2);
$proposal->insert_corigendum($_SESSION['user_id']);