
<form action="./connection/register-process.php" method="post" class="basic-grey">
    <h1>فرم ثبت نام
        <span>لطفا تمامی فیلد های موجود را پر کنید.</span>
    </h1>
    <label>
        <input id="name" type="number" name="username" placeholder="شماره دانشجویی" required>
    </label>

    <label>
        <input id="password" type="password" name="password" placeholder="رمز عبور" required>
    </label>

  <label>
        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="تایید رمز عبور" required>
    </label>

    <label>
        <input id="f_name" type="text" name="f_name" placeholder="نام" required>
    </label>
     <label>
        <input id="l_name" type="text" name="l_name" placeholder="نام خانوادگی" required>
    </label>


    <p>رشته و مقطع تحصیلی</p>
    <select name="field_level">
        <option value="s_bachelor">مهندسی کامیوتر نرم افزار - کارشناسی</option>
        <option value="s_pre_bachelor">مهندسی کامیوتر نرم افزار - کاردانی</option>
        <option value="s_to_bachelor">مهندسی کامیوتر نرم افزار - کاردانی به کارشناسی</option>
        <option value="it_bachelor">مهندسی کامیوتر ای تی - کارشناسی</option>
        <option value="it_pre_bachelor">مهندسی کامیوتر ای تی - کاردانی</option>
        <option value="it_to_bachelor">مهندسی کامیوتر ای تی - کاردانی به کارشناسی</option>
        <option value="h_bachelor">مهندسی کامیوتر سخت افزار -کارشناسی</option>
        <option value="h_pre_bachelor">مهندسی کامیوتر سخت افزار - کاردانی </option>
        <option value="h_to_bachelor">مهندسی کامیوتر سخت افزار - کاردانی به کارشناسی</option>
    </select>


    <label>
        <input type="submit" class="button" value="ورود به سیستم"/>
    </label>
    <div class="clear"></div>
    <p style="display: none" class="<?php if(isset($_GET['er'])){echo 'error_register';}?> ">خطایی در ثبت نام </p>


</form>