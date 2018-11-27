<?php
    require_once 'login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) die(mysql_fatal_error("OOPS"));

    echo <<<_END
<html>
    <head>
        <title>disMal Malicious File Check</title>
    </head>
    <body>
        <h1>disMal Malware Check</h1>

        <form margin: 0 auto; action="main.php" method="post" enctype='multipart/form-data'>
            Select File: <input type='file' name='filename' size='10'>
                         <input type='submit' value='Upload'>
        </form>
    </body>
</html>
_END;

    $conn->close();
    
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