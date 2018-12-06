<?php
    require_once 'login.php';
    $connection = new mysqli($hn, $un, $pw, $db);
    if ($connection->connect_error) die($connection->connect_error);
    $username = $password = "";
    echo <<<_END
<html>
    <head>
        <style>
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
                width: 100%;
            }

            body {
                display: table;
            }

            .center {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }
        </style>
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
            <h4><a href="sig.php">Signature Upload</a></h4>
            <h4><a href="dismal.php" onclick="destroy_session_and_data();">Logout</a></h4>
        </div>
    </body>
</html>
_END;
    } else {
        echo <<< _END
            <h4><div style=padding:25;">
                    <a href="auth.php">Staff Login</a>
            </div></h4>
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