<h1>پروپوزال های مورد تایید شما</h1>
<?php
$proposal = new proposal();
$proposal->setDb($con2);
$proposal->show_accepted_proposal($_SESSION['user_id']);?>
