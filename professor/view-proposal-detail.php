<?php
include_once '../inc/head.php';
?>

    <div class="main">
        <div class="header">
            <img src="../css/logo.jpg" alt="logo">
        </div>
        <?php
        $proposal=new proposal();
        if(isset($_REQUEST['pro_id'])){
            $proposal->setDb($con2);
            $proposal_id=$_REQUEST['pro_id'];
            $proposal->view_proposal_detail($proposal_id);
        }else{

            echo 'salam';
        }
        ?>

<div class="view-detail">
        <a href="../index.php?pr=view_proposal" class="button button-rounded button-tiny">بازگشت به عقب</a>
    </div>
        <div class="clear"></div>
    </div>
<?php         include_once '../inc/foot.php';
?>