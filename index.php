<?php
include_once 'inc/head.php';?>
<div class="main">
    <div class="header">
        <img src="css/logo.jpg" alt="logo">
    </div>
    <div class="side-bar">

        <?php
        if (!is_login()) {
            include 'authentication/login.php';
        } else {
            switch ($_SESSION['role']) {
                case 'student':
                    include 'student/st-side-bar.php';
                    break;
                case 'professor':
                    include 'professor/pr-side-bar.php';
                    break;
                case 'admin':
                    include 'admin/ad-side-bar.php';
                    break;

            }

        }
        ?>

    </div>
    <div class="content">
        <?php
        if (!is_login()) {
            include 'partial/welcome.php';
            if(isset($_GET['ind'])){
                include 'authentication/register.php';
            }
        }else {
            switch ($_SESSION['role']) {
                case 'student':
                    include 'student/student.php';
                    break;
                case 'professor':
                    include 'professor/professor.php';
                    break;
                case 'admin':
                    include 'admin/admin.php';
                    break;
            }

         } ?>

    </div>
    <div class="clear"></div>
</div>
<?php include_once 'inc/foot.php'; ?>

