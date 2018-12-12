<?php
    require_once 'login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) {
        die($conn->connect_error);
    }
    mysqli_set_charset($conn, "utf8mb4");

    $username = $password = $sig = "";
    
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
                Questionable File:<br><br><input type='file' name='filename' size='20'>
                <input type='submit' value='Upload'>
            </form>
_END;
    
    if (isset($_POST['logout'])) {
        destroy_session_and_data();
    } else {
        session_start();
    }
    if (isset($_SESSION['username'])) {
        if ($_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'] .$_SERVER['HTTP_USER_AGENT'])) {
            different_user();
        }
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
_END;
    } else {
        echo <<< _END
            <form action="auth.php" method="post" enctype='multipart/form-data'>
                <button type="submit">Staff Login</button>
            </form>
_END;
    }

    if ($_FILES) {
        $n = $_FILES['filename']['name'];
        move_uploaded_file($_FILES['filename']['tmp_name'], 'checks/'.$n) or die("<p><font color='red'><i>Must upload a file</i></font></p>");
        
        $ha = fopen('checks/'.$n, "rb") or die("<p><font color='red'><i>Could not open file</i></font></p>");
        if (flock($ha, LOCK_EX)) {
            $data = unpack("H20sig", fread($ha, "20"));
            flock($ha, LOCK_UN);
        }
        fclose($ha);

        unlink('checks/'.$n) or die("<p><font color='red'><i>Could not delete file</i></font></p>");

        $sig = mysql_entities_fix_string($conn, $data['sig']);

        $query = "SELECT * FROM mal WHERE sig='$sig';";

        $result = $conn->query($query);

        if (!$result) {
            echo "<p><font color='red'><i>SELECT failed: $query<br>" . $conn->error . "</i></font></p><br><br>";
        } else {
            $rows = $result->num_rows;

            if ($rows == 0) {
                echo "<p><font color='green'><i>File searched successfully! Your file is clean!</i></font></p><br><br>";
            } else {
                echo "<p><font color='red'><i>Your file is infected by one of the following:<br>";
                for ($i = 0; $i < $rows; ++$i) {
                    $result->data_seek($i);
                    $row = $result->fetch_array(MYSQLI_NUM);
                    echo $row[0] . "<br>";
                }
                echo "</i></font></p>";
            }
        }
        $result->close();
    }

    echo "</div></body></html>";

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

    function different_user() {
        $_SESSION = array();
        setcookie(session_name(), '', time() - 2592000, '/');
        session_destroy();
        echo <<< _END
        <form action="auth.php" method="post" enctype='multipart/form-data'>Error authenticating user: please <button type="submit">login</button> again!
</form>
_END;
    }
?>
