<?php
include_once '../inc/head.php';
$proposal=new proposal();
$proposal->setId($_POST['proposal_id']);
$proposal->setDb($con2);
$proposal->professor_delete_proposal($_SESSION['user_id']);