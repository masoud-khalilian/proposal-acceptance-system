<?php


if (isset($_GET['st'])) {
    $var = $_GET['st'];
} else {
    $var = 'wrong';
    include "student-welcome.php";
}


switch ($var) {
    case 'submit':
        include 'submit-proposal.php';
        break;
    case 'view_present_day':
        include 'professor-present-day.php';
        break;
    case 'sample':
        include 'sample-proposal.php';
        break;
    case 'guide':
        include 'guide-proposal.php';
        break;
 case 'track':
        include 'tracking_proposal.php';
        break;
}