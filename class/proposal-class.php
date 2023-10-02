<?php

class proposal
{

    public $id;
    public $title;
    public $content;
    public $submit_date;
    public $db;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }


    /*** @param mixed $db */
    public function setDb($db)
    {
        $this->db = $db;
    }


//    this function is for fetching proposal to show to professors in order to choose
    public function fetch_proposal($professor)
    {

        foreach ($this->db->query("SELECT * FROM proposal_pending WHERE professor_username = '$professor'") as $row) {
            $proposal_id = $row['proposal_id'];
            foreach ($this->db->query("SELECT * FROM propozal WHERE id = '$proposal_id'") as $row2) {
                $username = $row2['user_id'];
                foreach ($this->db->query("SELECT * FROM student WHERE username = '$username'") as $row3) {

                    echo '<div class="professor_view_proposal">';
                    echo '<div class="professor_view_proposal_box1">';
                    echo $row2['tittle'];

                    echo '</div>';
                    echo '<div class="professor_view_proposal_box2">';
                    echo $row3['f_name'] . ' ' . $row3['l_name'] . ' ' . $row2['user_id'];

                    echo '</div>';
                    echo '<div class="professor_view_proposal_box1">';
                    $student = new student();
                    $student->retrieve_field_lvl($row3['field_lvl']);
                    echo $student->getFieldLevel();
                    echo '</div>';
                    echo '<div class="professor_view_proposal_box2">';
                    $show_proposal = '<form action="./professor/view-proposal-detail.php" method="post">
                    <input type="hidden" value="' . $proposal_id . '" name="pro_id">

                <input type="submit" value="مشاهده" class="button button-rounded button-tiny">
                        </form>';
                    echo $show_proposal;

                    echo '</div>';

                    echo '</div>';


                }


            }


        }
    }


//this function is for showing full proposal to professor
    public function view_proposal_detail($proposal_id)
    {
        foreach ($this->db->query("SELECT * FROM propozal WHERE id = '$proposal_id'") as $row1) {
            $username = $row1['user_id'];
            foreach ($this->db->query("SELECT * FROM student WHERE username = '$username'") as $row2) {
                $f_name = $row2['f_name'];
                $l_name = $row2['l_name'];
                $username = $row2['username'];
                $student = new student();
                $student->retrieve_field_lvl($row2['field_lvl']);
                $field_lvl = $student->getFieldLevel();
            }
            $title = $row1['tittle'];
            $content = $row1['content'];
            $proposal_id_accept_address = '../professor/accept-proposal-process.php?proposal=' . $proposal_id;
            $result = '<div class="view-proposal-detail">
                 <h1>بسمه تعالی</h1>
                 <h3>فرم تعریف پروپوزال</h3>
                <div class="view-proposal-detail-meta">
            <table>
                <tr>
                    <td>نام و نام خانوادگی :' . $f_name . ' ' . $l_name . ' </td>
                    <td>شماره دانشجویی :' . $username . '</td>
                </tr>
                <tr>
                    <td>رشته و مقطع تحصیلی :' . $field_lvl . ' </td>
                    <td>زمان اخذ پروژه : ' . get_date() . '</td>
                </tr>
            
            </table>
        
            </div>
                <div>نام خانوادگی استاد راهنما : ' . $_SESSION['user_session'] . '</div>
                <div>عنوان پروژه:' . $title . '</div>
                <div class="view-proposal-detail-meta-content">' . $content . '
                
                <div class="view-detail">
                <a href="' . $proposal_id_accept_address . '" class="button button-rounded button-tiny">تایید پروپوزال</a></div>
                </div>
                
                </div>';
            echo $result;
            $professor_decision = '<div class="view-proposal-detail"><form action="../professor/proposal-corigendum.php" class="proposal-view-corigendum" method="post">
               <h3>برای نوشتن اصلاحیه متن را در جعبه زیر بنویسید و برروی ارسال کلیک کنید.</h3>
                 <input type="hidden" name="proposal_id" value="' . $proposal_id . '">
                 <textarea name="content" id="mytextarea" class="mytextarea" ></textarea>         
                 <input type="submit" class="button" value="ارسال اصلاحیه"></form>
                 <form action="delete-proposal.php" method="post">
                    <input type="hidden" name="proposal_id" value="' . $proposal_id . '">
                    <input type="hidden" name="professor_id" value="' . $_SESSION['user_id'] . '">
                    <input type="submit" class="button" value="حذف این پروپوزال از لیست خود">
                    </form>
                 
                  </div>';
            echo $professor_decision;


        }
    }


//    this function will run when professor accept a certain proposal .
// it will add professor id to proposal table and delete proposal form proposal_pending table
    public function insert_professor_to_proposal($proposal_id, $professor_id)
    {
        $sql = "UPDATE propozal SET professor_id= '$professor_id' WHERE id='$proposal_id'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        foreach ($this->db->query("SELECT * FROM professor WHERE username = '$professor_id'") as $row1) {
            $capacity = $row1['capacity'];
        }
        $one_less = $capacity - 1;
        $sql2 = "UPDATE professor SET capacity= '$one_less' WHERE username='$professor_id'";
        $stmt1 = $this->db->prepare($sql2);
        $stmt1->execute();
        $sql3 = "DELETE FROM proposal_pending WHERE proposal_id=$proposal_id";
        $this->db->exec($sql3);

    }


//    this function is for professor to tell student how to correct their proposal
//    it works with writing the corection in proposal pending table
//    it gets the content proposal id and professor data to update the data base
    public function insert_corigendum($professor)
    {
        $sql = "UPDATE proposal_pending SET corrigendum= '$this->content' WHERE proposal_id='$this->id' AND professor_username='$professor'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

    }


    //this function is helping professors in order to remove the proposal from their list
    public function professor_delete_proposal($professor_id)
    {
        $sql = "DELETE FROM proposal_pending WHERE professor_username='$professor_id' AND proposal_id='$this->id'";
        $this->db->exec($sql);
    }


    //this function is for showing the accepted proposal to professor
    public function show_accepted_proposal($professor_username)
    {
        foreach ($this->db->query("SELECT * FROM propozal WHERE professor_id ='$professor_username'") as $row1) {
            $student_username = $row1['user_id'];

            foreach ($this->db->query("SELECT * FROM student WHERE username ='$student_username'") as $row2) {
                echo '<div class="professor_view_proposal">';
                echo '<div class="professor_view_proposal_box1">';
                echo $row1['tittle'];

                echo '</div>';
                echo '<div class="professor_view_proposal_box2">';
                echo $row2['f_name'] . ' ' . $row2['l_name'] . ' ' . $row1['user_id'];

                echo '</div>';
                echo '<div class="professor_view_proposal_box1">';
                $student = new student();
                $student->retrieve_field_lvl($row2['field_lvl']);
                echo $student->getFieldLevel();

                echo '</div>';
                echo '<div class="professor_view_proposal_box2">';
                $show_proposal = '<form action="./professor/view-proposal-detail.php" method="post">
                    <input type="hidden" value="' . $row1['id'] . '" name="pro_id">

                <input type="submit" value="مشاهده" class="button button-rounded button-tiny">
                        </form>';
                echo $show_proposal;
                echo '</div>';
                echo '</div>';


            }


        }


    }

}
