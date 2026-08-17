<h1>نمایش پروپوزال های ارسالی برای شما</h1>
<p>برای دیدن , تایید و یا ارسال اصلاحیه پروپوزال ها روی دکمه نمایش کلید کلیک کنید</p>

<?php
$proposal = new proposal();
$proposal->setDb($con2);
$proposal->fetch_proposal($_SESSION['user_id']);