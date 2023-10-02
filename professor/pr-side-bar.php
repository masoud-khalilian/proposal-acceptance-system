<div class="side-bar-header">
    <div class="info">
        <?php
        $p_professor = new professor();
        $p_professor->setDb($con2);
        $p_professor->setData($_SESSION['user_id']);
        $w1=$p_professor->getFName();
        $w2=$p_professor->getLName();
        $w3=$p_professor->getCapacity();
        ?>
        <p>  نام و نام خانوادگی : <?php echo '<span>'.$w1.' '.$w2.'</span>' ?></p>
        <p>شماره پرسنلی : <?php echo '<span>'.$_SESSION['user_id'].'</span>' ; ?></p>
        <p>ظرفیت باقیمانده : <?php echo '<span>'.$w3.'</span>' ; ?></p>

    </div>

    <div class="logout">

        <a href="test.php"><i class="fa fa-sign-out" aria-hidden="true"></i></a>
        <p>خروج</p>
    </div>
    <div class="clear"></div>
</div>



<div class="nav">
    <ul>
        <li class="<?php if ($_GET['pr']===null){echo 'active';}?>"><a href="../21-uni-project/index.php"><i class="fa fa-home" aria-hidden="true"></i>  خانه</a></li>
        <li class="<?php if ($_GET['pr']==='view_proposal'){echo 'active';}?>"><a href="../21-uni-project/index.php?pr=view_proposal"><i class="fa fa-eye" aria-hidden="true"></i>  دیدن لیست پروپوزال های ارسالی</a></li>
        <li class="<?php if ($_GET['pr']==='guide'){echo 'active';}?>"><a href="../21-uni-project/index.php?pr=guide"><i class="fa fa-map-signs" aria-hidden="true"></i>  راهنمایی استفاده از سیستم</a></li>
        <li class="<?php if ($_GET['pr']==='accepted_proposal'){echo 'active';}?>"><a href="../21-uni-project/index.php?pr=accepted_proposal"><i class="fa fa-check-square" aria-hidden="true"></i>  پروپوزال های تاییدی شما</a></li>
    </ul>
</div>