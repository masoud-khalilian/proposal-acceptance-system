<?php
echo '<h1>پیگیری پروپوزال</h1>';
echo '<br>';
$student=new student();
$student->setDb($con2);
$student->setUsername($_SESSION['user_id']);
$student->student_track_proposal();
