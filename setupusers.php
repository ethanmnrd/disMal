<?php // setupusers.php
    require_once 'login.php';
    $connection = new mysqli($hn, $un, $pw, $db);
    if ($connection->connect_error) die($connection->connect_error);

    $query = "CREATE TABLE IF NOT EXISTS users (
        uid SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
        username VARCHAR(32) NOT NULL UNIQUE,
        password VARCHAR(32) NOT NULL,
        PRIMARY KEY(uid)
        ) ENGINE MyISAM";

    $result = $connection->query($query);
    if (!$result) die($connection->error);

    $salt1 = "qm&h*"; $salt2 = "pg!@";

    $username = 'maladmin';
    $password = 'malWare1sbad';
    $token = hash('ripemd128', "$salt1$password$salt2");

    add_user($connection, $username, $token);

    function add_user($connection, $usn, $paw)
    {
        $query = "INSERT INTO users (username, password) VALUES('$usn', '$paw')";
        $result = $connection->query($query);
        if (!$result) die($connection->error);
    }
?>