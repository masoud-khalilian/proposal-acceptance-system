<?php

$dsn = 'mysql:host=localhost;dbname=uni_pp';
$username = 'masoud';
$password = '111qweasdzxc';
try {

    $con = new PDO($dsn,$username,$password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

} catch (exception $ex) {
    echo 'not connected : '.$ex->getMessage();

};





$dsni = 'mysql:host=localhost;dbname=uni_pp2';
$username = 'masoud';
$password = '111qweasdzxc';
try {

    $con2 = new PDO($dsni,$username,$password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $con2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

} catch (exception $ex) {
    echo 'not connected : '.$ex->getMessage();

};