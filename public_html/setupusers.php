<?php // setupusers.php
    require_once 'login.php';
    $connection = new mysqli($hn, $un, $pw, $db);
    if ($connection->connect_error) die($connection->connect_error);

    $query = "CREATE TABLE users (
        uid SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
        username VARCHAR(32) NOT NULL UNIQUE,
        password VARCHAR(32) NOT NULL,
        fname VARCHAR(32) NOT NULL,
        lname VARCHAR(32) NOT NULL,
        PRIMARY KEY(uid)
        ) ENGINE MyISAM";

    $result = $connection->query($query);
    if (!$result) die($connection->error);

    $salt1 = "qm&h*"; $salt2 = "pg!@";

    $fname = 'Bill';
    $lname = 'Smith';
    $username = 'bsmith';
    $password = 'mysecret';
    $token = hash('ripemd128', "$salt1$password$salt2");

    add_user($connection, $username, $token, $fname, $lname);

    $fname = 'Pauline';
    $lname = 'Jones';
    $username = 'pjones';
    $password = 'acrobat';
    $token = hash('ripemd128', "$salt1$password$salt2");

    add_user($connection, $username, $token, $fname, $lname);

    function add_user($connection, $usn, $paw, $fn, $ln)
    {
        $query = "INSERT INTO users (username, password, fname, lname) VALUES('$usn', '$paw', '$fn', '$ln')";
        $result = $connection->query($query);
        if (!$result) die($connection->error);
    }
?>