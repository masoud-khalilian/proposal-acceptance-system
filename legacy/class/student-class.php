<?php

class student extends member
{
    public $field_level;

    /**
     * @return mixed
     */
    public function getFieldLevel()
    {
        return $this->field_level;
    }

    /**
     * @param mixed $field_level
     */
    public function setFieldLevel($field_level)
    {
        $this->field_level = $field_level;
    }


//    function for turning coded data to real data
    public function retrieve_field_lvl($filed_lvl)
    {
        switch ($filed_lvl) {
            case 's_bachelor':
                $this->field_level = 'مهندسی کامیوتر نرم افزار - کارشناسی';
                break;
            case 's_pre_bachelor':
                $this->field_level = 'مهندسی کامیوتر نرم افزار - کاردانی';
                break;
            case 's_to_bachelor':
                $this->field_level = 'مهندسی کامیوتر نرم افزار - کاردانی به کارشناسی';
                break;
            case 'it_bachelor':
                $this->field_level = 'مهندسی کامیوتر ای تی - کارشناسی';
                break;
            case 'it_pre_bachelor':
                $this->field_level = 'مهندسی کامیوتر ای تی - کاردانی';
                break;
            case 'it_to_bachelor':
                $this->field_level = 'مهندسی کامیوتر ای تی - کاردانی به کارشناسی';
                break;
            case 'h_bachelor':
                $this->field_level = 'مهندسی کامیوتر سخت افزار -کارشناسی';
                break;
            case 'h_pre_bachelor':
                $this->field_level = 'مهندسی کامیوتر سخت افزار - کاردانی ';
                break;
            case 'h_to_bachelor':
                $this->field_level = 'مهندسی کامیوتر سخت افزار - کاردانی به کارشناسی';
                break;


        }

    }

//this function help to set data to make auto complete proposal and also is used for info in side bar
    public function setData($username)
    {

        foreach ($this->db->query("SELECT * FROM student WHERE username = '$username'") as $row) {
            $this->f_name = $row['f_name'];
            $this->l_name = $row['l_name'];
            $this->retrieve_field_lvl($row['field_lvl']);

        }
    }

//this function will create proposal and send some data to proposal pending
    public function send_proposal($title, $content, $date_creation, $professor_ch)
    {
        try {
            $sql = "INSERT INTO propozal (user_id,tittle,content,date_creation)VALUES(:user_id,:tittle,:content,:date_creation) ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $this->username);
            $stmt->bindParam(':tittle', $title);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':date_creation', $date_creation);
            $stmt->execute();

            foreach ($this->db->query("SELECT * FROM propozal WHERE user_id = '$this->username'") as $row) {
            }
            foreach ($professor_ch as $item) {
                $sql2 = "INSERT INTO proposal_pending (proposal_id,student_username,professor_username)VALUES(:proposal_id,:student_username,:professor_username) ";
                $stmt2 = $this->db->prepare($sql2);
                $stmt2->bindParam(':proposal_id', $row['id']);
                $stmt2->bindParam(':student_username', $this->username);
                $stmt2->bindParam(':professor_username', $item);
                $stmt2->execute();


            }
            return true;
        } catch (PDOException $e) {
            return $e;
        }


    }

    //this function is for student to track his/her process of proposal it will showed at tracking_proposal is student file
    //this function has 3 respond to student 1InProcess 2Accepted 3DoesHaveCorrigendum
    public function student_track_proposal()
    {
        foreach ($this->db->query("SELECT * FROM propozal WHERE user_id = '$this->username'") as $row) {
            $professor = $row['professor_id'];
            foreach ($this->db->query("SELECT * FROM professor WHERE username = '$professor'") as $row2) {
                $l_name = $row2['l_name'];
                $f_name = $row2['f_name'];
                $email = $row2['email'];
                $p_day = $row2['day_present'];
            }
        }
        if (isset($row['professor_id'])) {
            echo '<div class="accept_proposal_respond"><p>';
            echo 'پروپوزال شما توسط استاد ' . $f_name . ' ' . $l_name . ' تایید شده است.';
            echo '<br>';
            echo 'شما می توانید در ' . '<span>' . $p_day . '</span>' . ' برای گرفتن امضا مراجعه کنید.';
            echo '<br>';
            echo 'همچنین می توانید با این آدرس ایمیل با استاد ارتباط برقرار کنید:' . '<span>' . $email . '</span>';
            echo '</p></div>';
        } else {
            foreach ($this->db->query("SELECT * FROM propozal WHERE user_id = '$this->username'") as $row5) {}
                if (isset($row5['user_id'])){
                $tttt=0;
            echo '<p>اساتید برای پروپوزال شما اصلاحیه های زیر را فرستاند لطفا پس از مشاهده پروپوزال خود را اصلاح و سپس دوباره آنرا ارسال کنید.</p>';
            foreach ($this->db->query("SELECT * FROM proposal_pending WHERE student_username = '$this->username'") as $row3) {
                $professor = $row3['professor_username'];
                if (empty($row3['corrigendum'])) {
                    $tttt+=1;

                    echo $tttt.'-درحال بررسی'.'<br>';

                } else {
                    foreach ($this->db->query("SELECT * FROM professor WHERE username = '$professor'") as $row4) {
                        $l_name = $row4['l_name'];
                        $f_name = $row4['f_name'];
                        $email = $row4['email'];
                        echo '<div class="professor_view_proposal">';
                        echo '<div class="professor_view_proposal_box1">';
                        echo $row3['corrigendum'];
                        echo '</div>';
                        echo '<div class="professor_view_proposal_box2">';
                        echo $f_name . ' ' . $l_name;
                        echo '</div>';
                        echo '<div class="professor_view_proposal_box1">';
                        echo $email;
                        echo '</div>';
                        echo '</div>';
                    }
                }
            }
        }else{
                    echo 'ابتدا پروپوزال خود را ثبت کنید';
                }}
    }


//    this function is for checking that student has proposal or not if it does have how much time is passed
    public function check_has_proposal($student_id)
    {
        foreach ($this->db->query("SELECT * FROM propozal WHERE user_id = '$student_id'") as $row1) {
            $submit_time = $row1['date_creation'];
        }
        if (empty($submit_time)) {
            return 'no_proposal';
        } else {
            return $submit_time;
        }


    }


//login student
    public function student_login()
    {
        try {
            foreach ($this->db->query("SELECT * FROM student WHERE username = '$this->username' and password = '$this->password'") as $row) {
            }
            if (isset($row['id'])) {
                $_SESSION['user_session'] = $row['l_name'];
                $_SESSION['user_id'] = $row['username'];
                $_SESSION['role'] = $this->role;

            }
        } catch (PDOException $e) {
            echo 'the user name and password are wrong2';
        }


    }

//register new student
    public function student_register()
    {
        $sql = "INSERT INTO student (username,f_name,password,l_name,field_lvl)VALUES(:username,:f_name,:password,:l_name,:field_lvl) ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':f_name', $this->f_name);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':l_name', $this->l_name);
        $stmt->bindParam(':field_lvl', $this->field_level);
        $stmt->execute();


    }
}