<?php
include 'C:\xampp\htdocs\21-uni-project/connection/conn.php';

function home_directory(){
    echo '\21-uni-project';
}
session_start();

include_once "class/member-class.php";
include_once "class/student-class.php";
include_once "class/professor-class.php";
include_once "class/proposal-class.php";

function is_login(){
    if(isset($_SESSION['user_session'])){
        return true;
    }else{
        return false;
    }

}

function get_date(){

    return 'تابستان 96';
}

