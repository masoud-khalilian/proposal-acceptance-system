<?php

class professor extends member
{
    public $present_day;
    public $status;
    public $capacity;


    /**
     * @param mixed $present_day
     */
    public function setPresentDay($present_day)
    {
        $this->present_day = $present_day;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


    /**
     * @return mixed
     */
    public function getCapacity()
    {
        return $this->capacity;
    }


    public function professor_login()
    {

        try {
            foreach ($this->db->query("SELECT * FROM professor WHERE username = '$this->username' and password = '$this->password'") as $row) {
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

//    this function is for showing the student how many capacity a professor has and also fill the table to choos professor from it
    public function professor_show_data($con2)
    {

        foreach ($con2->query("SELECT * FROM professor WHERE capacity > '0'") as $row) {

            $this->username = $row['username'];
            $this->l_name = $row['l_name'];
            $this->f_name = $row['f_name'];
            $this->capacity = $row['capacity'];

            $result = "<tr><td><input type='checkbox' value='$this->username' name='professor_ch[]'> </td><td>$this->f_name  $this->l_name</td><td> $this->capacity</td>";
            echo $result;

        }


    }

    public function setData($username)
    {

        foreach ($this->db->query("SELECT * FROM professor WHERE username = '$username'") as $row) {
            $this->f_name = $row['f_name'];
            $this->l_name = $row['l_name'];
            $this->capacity=$row['capacity'];
        }
    }


}