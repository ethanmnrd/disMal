<?php // setupusers.php
    require_once 'login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) die($conn->connect_error);

    $query = "CREATE TABLE IF NOT EXISTS users (
        uid SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
        username VARCHAR(32) NOT NULL UNIQUE,
        password VARCHAR(32) NOT NULL,
        PRIMARY KEY(uid)
        ) ENGINE MyISAM";

    $result = $conn->query($query);
    if (!$result) die($conn->error);

    $salt1 = "qm&h*"; $salt2 = "pg!@";

    $username = 'maladmin';
    $password = 'malWare1sbad';
    $token = hash('ripemd128', "$salt1$password$salt2");

    add_user($conn, $username, $token);

    $query = "CREATE TABLE IF NOT EXISTS mal (
            name VARCHAR(25) NOT NULL,
            sig VARCHAR(20) NOT NULL,
            PRIMARY KEY(sig)) ENGINE MyISAM";

    $result = $conn->query($query);
    if (!$result) die ($conn->error);

    function add_user($conn, $usn, $paw)
    {
        $query = "INSERT INTO users (username, password) VALUES('$usn', '$paw')";
        $result = $conn->query($query);
        if (!$result) die($conn->error);
    }
?>
