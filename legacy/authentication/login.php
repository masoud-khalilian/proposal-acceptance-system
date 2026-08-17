<form action="./connection/login-process.php" method="post" class="basic-grey">
    <h1>فرم ورود به سایت
        <span>لطفا تمامی فیلد های موجود را پر کنید.</span>
    </h1>
    <label>
        <input id="name" type="text" name="username" placeholder="کد کاربری" required>
    </label>

    <label>
        <input id="password" type="password" name="password" placeholder="رمز عبور" required>
    </label>

    <div class="radio_label">
        <label for="student">
            <input type="radio" name="role" value="student" checked="checked" id="student">
            دانشجو
        </label>
        <label for="professor">
            <input type="radio" name="role" value="professor" id="professor">
            استاد
        </label>
        <label for="admin">
            <input type="radio" name="role" value="admin" id="admin">

            ادمین
        </label>
    </div>
    <label>
        <input type="submit" class="button" value="ورود به سیستم"/>
    </label>
    <div class="clear"></div>

</form>
<p style="display: none" class="<?php if(isset($_GET['er'])){echo 'error_register';}?> ">خطایی در ورود رخ داده است. <br> لطفا از اطلاعات وارد شده اطمینان حاصل کنید. </p>
