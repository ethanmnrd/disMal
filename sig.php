<?php
    require_once 'login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) die($conn->connect_error);
    
    $name = $sig = "";

    echo <<< _END
<html>
    <head>
        <link rel="stylesheet" href="styles.css">
        <title>Malware Signature Upload</title>
    </head>
_END;

    if (isset($_POST['logout']))
    {
        destroy_session_and_data();
    } else {
        session_start();
    }
    if (isset($_SESSION['username']))
    {
        echo <<< _END
    <body>
        <div class="center">
            <h1>Signature Upload</h1>
            <form margin: 0 auto; action="sig.php" method="post" enctype='multipart/form-data'>
                Name: <br><input type='text' name='name' size='25'><br><br>
                Signature: <br><input type='text' name='sig' size='25'><br><br>
                <input type='submit' value='Upload'>
            </form>
            <form action="dismal.php" method="post" enctype='multipart/form-data'>
                <button type="submit">File Upload</button>
            </form>
            <form action="dismal.php" method="post" enctype='multipart/form-data'>
                <input type="hidden" name='logout' value='yes'>
                <button type="submit">Logout</button>
            </form>
_END;

        if (isset($_POST['name']) && isset($_POST['sig']))
        {
            $name = mysql_entities_fix_string($conn, $_POST['name']);
            $sig = mysql_entities_fix_string($conn, $_POST['sig']);

            $query = "INSERT INTO mal (name, sig) VALUES ('$name', '$sig');";

            $result = $conn->query($query);

            if (!$result)
            {
                echo "<font color='red'>INSERT failed: $query<br>" . $conn->error . "</font><br><br></div></body></html>";
            }
            else
            {
                echo "<h4><font color='green'>Uploaded Malware signature successfully!</font></h4><br><br></div></body></html>";
            }
        }
    } else {
        echo <<< _END
    <body>
        <form padding: 5; action="auth.php" method="post">
            <input type="hidden" name="siglogin">
            You must be <button type="submit">logged in</button> to view this page!
        </form> 
    </body>
</html>
_END;
    }

    $conn->close();

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
