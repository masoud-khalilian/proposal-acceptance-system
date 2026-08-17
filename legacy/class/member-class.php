<?php
class member {
    public $f_name;
    public $l_name;
    public $username;
    public $password;
    public $role;
    public $db;


    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }



    /**
     * @param mixed $db
     */
    public function setDb($db)
    {
        $this->db = $db;
    }

    /**
     * @param mixed $f_name
     */
    public function setFName($f_name)
    {
        $this->f_name = $f_name;
    }

    /**
     * @param mixed $l_name
     */
    public function setLName($l_name)
    {
        $this->l_name = $l_name;
    }

    /**
     * @param mixed $id_number
     */
    public function setIdNumber($id_number)
    {
        $this->id_number = $id_number;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param mixed $phone_number
     */
    public function setPhoneNumber($phone_number)
    {
        $this->phone_number = $phone_number;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

//        getters functions

    /**
     * @return mixed
     */
    public function getFName()
    {
        return $this->f_name;
    }

    /**
     * @return mixed
     */
    public function getLName()
    {
        return $this->l_name;
    }


    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }


    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return mixed
     */
    public function getDb()
    {
        return $this->db;
    }

    public function member_login(){
        try
        {
            $res=$this->db->prepare("SELECT * FROM t_student WHERE student_id = :id_number and password = :pass");
            $res->execute(array(':id_number'=>$this->username , ':pass'=>$this->password));
            $row = $res->fetchAll();

            foreach ($this->db->query("SELECT * FROM t_student WHERE student_id = '$this->username' and password = '$this->password'") as $row) {
//                print "Name: " .  $row['name'] . "\n";
//                print "id: " .  $row['student_id'] . "\n";
            }
            if(!empty($row)){
                $_SESSION['user_session'] = $row['l_name'];
                $_SESSION['user_id'] = $row['student_id'];
                $_SESSION['role']= $this->role;
                return true;
            }else{
                echo 'the user name and password are wrong';
            }

        }
        catch(PDOException $e)
        {
            echo 'the user name and password are wrong2';

        }

    }

}
