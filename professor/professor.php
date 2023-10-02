<?php


if (isset($_GET['pr'])) {
    $var = $_GET['pr'];
} else {
    $var = 'wrong';
    include "professor-welcome.php";
}


switch ($var) {
    case 'view_proposal':
        include 'view-proposal.php';
        break;
    case 'notifications':
        include 'notifications.php';
        break;
    case 'submit_present_day':
        include 'submit-present-day.php';
        break;
    case 'guide':
        include 'guide.php';
        break;
    case   'accepted_proposal':
        include 'accepted-proposal.php';
        break;
}