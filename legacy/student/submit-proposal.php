<?php $student = new student();
$username = $_SESSION['user_id'];
$student->setDb($con2);
$student->setData($username);


$submit_time = $student->check_has_proposal($username);
$twodays = 60 * 60 * 24 * 2;
$after2days = $submit_time + $twodays;
$current_time = mktime();
$until = $after2days - $current_time;
$until_h = ceil($until / (60 * 60));
if ($after2days < $current_time || $submit_time === 'no_proposal') {
    ?>
    <div class="submit-proposal">
        <h1>بسمه تعالی</h1>
        <h3>فرم تعریف پروژه</h3>
        <div class="submit-proposal-content">
            <div class="submit-proposal-core sub-inline">
                <span>نام و نام خانوادگی : </span>
                <?php echo $student->getFName() . ' ' . $student->getLName() ?>
            </div>

            <div class="submit-proposal-core sub-inline">
                <span>شماره دانشجویی : </span>
                <?php echo $username ?>
            </div>

            <div class="submit-proposal-core sub-inline">
                <span>رشته و مقطع تحصیلی : </span>
                <?php echo $student->getFieldLevel() ?>
            </div>

            <div class="submit-proposal-core sub-inline">
                <span>تاریخ اخذ پروژه : </span>
                <?php echo get_date() ?>
            </div>
            <hr>
            <div class="submit-proposal-core">
                <span>نام و نام خانوادگی استاد: </span>
                <?php echo 'در قسمت پایین تنظیم کنید' ?>

            </div>

            <form action="./student/proposal-send-process.php" method="post" class="st-submit-proposal">
                <input type="hidden" value="<?= $username ?>" name="username">
                <div class="submit-proposal-core">
                    <label for="subject">
                        <span>عنوان : </span>

                        <input type="text" name="title" placeholder="عنوان مناسب" id="subject">
                    </label>
                </div>
                <div class="submit-proposal-core">

                    <label for="brief_summary">
                        <textarea name="content" id="mytextarea" class="mytextarea"></textarea>
                    </label>
                </div>
                <hr>
                <div class="choose-professor">
                    <h3>انتخاب استاد</h3>
                    <table>
                        <th>انتخاب</th>
                        <th>اسم استاد</th>
                        <th>ظرفیت</th>
                        <?php
                        $professor = new professor();
                        echo '<tr>';
                        $professor->professor_show_data($con2);
                        echo '</tr>';

                        ?>
                    </table>
                </div>
                <hr>
                <button class="button button-glow button-border button-rounded button-primary">ادامه مراحل</button>

            </form>
        </div>
    </div>
<?php } else {
    echo 'شما بعد از ثبت پروپوزال باید تا 48 ساعت صبر کنید.';
    echo 'زمان باقیمانده ' . $until_h . 'ساعت';
}
?>