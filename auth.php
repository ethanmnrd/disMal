<?php // auth.php
    require_once 'login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) {
        die($conn->connect_error);
    }
    
    echo <<< _END
<html>
    <head>
        <link rel="stylesheet" href="styles.css">
        <title>disMal Login</title>
    </head>
    <body>
    <div class="center">
_END;

    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        $un_temp = mysql_entities_fix_string($conn, $_SERVER['PHP_AUTH_USER']);
        $pw_temp = mysql_entities_fix_string($conn, $_SERVER['PHP_AUTH_PW']);
        
        $query = "SELECT * FROM users WHERE username='$un_temp'";
        $result = $conn->query($query);
        
        if (!$result) {
            die($conn->error);
        } elseif ($result->num_rows) {
            $row = $result->fetch_array(MYSQLI_NUM);
            $result->close();
            $salt1 = "qm&h*";
            $salt2 = "pg!@";
            $token = hash('ripemd128', "$salt1$pw_temp$salt2");
            
            if ($token == $row[2]) {
                ini_set('session.gc_maxlifetime', 60 * 60 * 24);
                session_start();
                if (!isset($_SESSION['initiated']))
                {
                    session_regenerate_id();
                    $_SESSION['initiated'] = 1;
                }
                $_SESSION['username'] = $un_temp;
                $_SESSION['password'] = $pw_temp;
                $_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'] .$_SERVER['HTTP_USER_AGENT']);
                
                if (isset($_POST['siglogin'])) {
                    echo "<form action='sig.php' method='post'>";
                } else {
                    echo "<form action='dismal.php' method='post'>";
                }
                echo "<h3>You are now logged in as '$row[1]'<br><br>
                        <button type='submit'>Click here to continue</button></h3>
                    </form>";
            } else {
                die("Invalid username/password combination");
            }
        } else {
            die("Invalid username/password combination");
        }
    } else {
        header('WWW-Authenticate: Basic realm="Restricted Section"');
        header('HTTP/1.0 401 Unauthorized');
        die("Please enter your username and password");
    }
    $conn->close();
    
    echo "</div></body></html>";

    function mysql_entities_fix_string($conn, $string)
    {
        return htmlentities(mysql_fix_string($conn, $string));
    }
    
    function mysql_fix_string($conn, $string)
    {
        if (get_magic_quotes_gpc()) {
            $string = stripslashes($string);
        }
        return $conn->real_escape_string($string);
    }
?>
