<div class="side-bar-header">
    <div class="info">
        <?php
        $s_student = new student();
        $s_student->setDb($con2);
        $s_student->setData($_SESSION['user_id']);
        $w1=$s_student->getFName();
        $w2=$s_student->getLName();
        $w3=$s_student->getFieldLevel();
        ?>
        <p>  نام و نام خانوادگی : <?php echo '<span>'.$w1.' '.$w2.'</span>' ?></p>
        <p>شماره دانشجویی : <?php echo '<span>'.$_SESSION['user_id'].'</span>' ; ?></p>
        <p>رشته و مقطع تحصیلی : <?php echo '<span>'.$w3.'</span>' ; ?></p>

    </div>

    <div class="logout">

        <a href="test.php"><i class="fa fa-sign-out" aria-hidden="true"></i></a>
        <p>خروج</p>
    </div>
     <div class="clear"></div>
</div>

<div class="nav">
    <ul>
        <li class="<?php if ($_GET['st']===null){echo 'active';}?>"><a href="../21-uni-project/index.php"><i class="fa fa-home" aria-hidden="true"></i>  خانه</a></li>
        <li class="<?php if ($_GET['st']==='submit'){echo 'active';}?>"><a href="../21-uni-project/index.php?st=submit"><i class="fa fa-pencil-square" aria-hidden="true"></i>  ثبت پروپوزال</a></li>
        <li class="<?php if ($_GET['st']==='track'){echo 'active';}?>"><a href="../21-uni-project/index.php?st=track"><i class="fa fa-cog" aria-hidden="true"></i>  پیگیری پروپوزال</a></li>
        <li class="<?php if ($_GET['st']==='view_present_day'){echo 'active';}?>"><a href="../21-uni-project/index.php?st=view_present_day"><i class="fa fa-binoculars" aria-hidden="true"></i>  دیدن روز حضور اساتید</a></li>
<!--        <li class="--><?php //if ($_GET['st']==='sample'){echo 'active';}?><!--"><a href="../21-uni-project/index.php?st=sample"><i class="fa fa-eye" aria-hidden="true"></i>  بازدید نمونه پروپوزال</a></li>-->
        <li class="<?php if ($_GET['st']==='guide'){echo 'active';}?>"><a href="../21-uni-project/index.php?st=guide"><i class="fa fa-map-signs" aria-hidden="true"></i>  راهنمایی نگارش پروپزال</a></li>
    </ul>
</div>