<?php // auth.php
    require_once 'login.php';
    $connection = new mysqli($hn, $un, $pw, $db);
    if ($connection->connect_error) die($connection->connect_error);
    
    echo <<< _END
<html>
    <head>
        <link rel="stylesheet" href="styles.css">
        <title>disMal Login</title>
    </head>
    <body>
    <div class="center">
_END;

    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
    {
        $un_temp = mysql_entities_fix_string($connection, $_SERVER['PHP_AUTH_USER']);
        $pw_temp = mysql_entities_fix_string($connection, $_SERVER['PHP_AUTH_PW']);
        
        $query = "SELECT * FROM users WHERE username='$un_temp'";
        $result = $connection->query($query);
        
        if (!$result) die($connection->error);
        elseif ($result->num_rows)
        {
            $row = $result->fetch_array(MYSQLI_NUM);
            $result->close();
            $salt1 = "qm&h*"; $salt2 = "pg!@";
            $token = hash('ripemd128', "$salt1$pw_temp$salt2");
            
            if ($token == $row[2])
            {
                session_start();
                $_SESSION['username'] = $un_temp;
                $_SESSION['password'] = $pw_temp;
                
                if(isset($_POST['siglogin']))
                {
                    echo "<form action='sig.php' method='post'>";
                }
                else
                {
                    echo "<form action='dismal.php' method='post'>";
                }
                echo "You are now logged in as '$row[1]'<br><br>
                        <button type='submit'>Click here to continue</button> 
                    </form>";
            }
            else die("Invalid username/password combination");
        }
        else die("Invalid username/password combination");
    }
    else
    { // if ($_SERVER['PHP_AUTH_USER’])  and  ($_SERVER['PHP_AUTH_PW’]) are not set
        header('WWW-Authenticate: Basic realm="Restricted Section"');
        header('HTTP/1.0 401 Unauthorized');
        die ("Please enter your username and password");
    }
    $connection->close();
    
    echo "</div></body></html>";

    function mysql_entities_fix_string($connection, $string)
    {
        return htmlentities(mysql_fix_string($connection, $string));
    }
    
    function mysql_fix_string($connection, $string)
    {
        if (get_magic_quotes_gpc()) $string = stripslashes($string);
            return $connection->real_escape_string($string);
    }
?> 