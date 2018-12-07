<?php
    require_once 'login.php';
    $connection = new mysqli($hn, $un, $pw, $db);
    if ($connection->connect_error) die($connection->connect_error);
    $username = $password = "";
    echo <<<_END
<html>
    <head>
        <link rel="stylesheet" href="styles.css">
        <title>disMal Malicious File Check</title>
    </head>
    <body>
        <div class="center">
            <h1>disMal Malware Check</h1>

            <form margin: 0 auto; action="dismal.php" method="post" enctype='multipart/form-data'>
                Select File <input type='file' name='filename' size='10'>
                            <input type='submit' value='Upload'>
            </form>
_END;
    
    if (isset($_POST['logout']))
    {
        destroy_session_and_data();
    } else {
        session_start();
    }
    if (isset($_SESSION['username']))
    {
        $username = $_SESSION['username'];
        $password = $_SESSION['password'];

        echo <<< _END
            <form action="sig.php" method="post" enctype='multipart/form-data'>
                <button type="submit">Signature Upload</button>
            </form>
            <form action="dismal.php" method="post" enctype='multipart/form-data'>
                <input type="hidden" name='logout' value='yes'>
                <button type="submit">Logout</button>
            </form>
        </div>
    </body>
</html> 
_END;
    } else {
        echo <<< _END
            <form action="auth.php" method="post" enctype='multipart/form-data'>
                <button type="submit">Staff Login</button>
            </form>
        </div>
    </body>
</html>
_END;
    }

    if ($_FILES)
    {
        $name = "";
        $name = $_FILES['filename']['name'] or die ("Must upload a file.");
        
    }

    $connection->close();

    function destroy_session_and_data()
    {
        session_start();
        $_SESSION = array();
        setcookie(session_name(), '', time() - 2592000, '/');
        session_destroy();
    }

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
