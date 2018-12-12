<?php
    require_once 'login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) {
        die($conn->connect_error);
    }
    mysqli_set_charset($conn, "utf8mb4");

    $name = $sig = $fail = "";

    echo <<< _END
<html>
    <head>
        <link rel="stylesheet" href="styles.css">
        <title>Malware Signature Upload</title>
_END;

    session_start();
    if (isset($_SESSION['username'])) {
        if ($_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'] .$_SERVER['HTTP_USER_AGENT'])) {
            different_user();
        }
        echo <<< _END
        <script>
            function validate(form)
            {
                fail = validateName(form.name.value)

                if (fail == "") return true
                else { alert(fail); return false }
            }

            function validateName(field)
            {
                if (field == "") return "No malware name was entered.\n"
                else if (/[^a-zA-Z0-9_-]/.test(field))
                    return "Only A-Z, a-z, 0-9, - and _ allowed in malware names.\n"
                return ""
            }
        </script>
    </head>
    <body>
        <div class="center">
            <h1>Signature Upload</h1>
            <form method="post" action="sig.php" onSubmit="return validate(this)" enctype='multipart/form-data'>
                Malware Name: <br><input type='text' maxlength="20" name='name' value="$name" size='25'><br><br>    
                Malware File: <br><br><input type='file' name='filename' size='20'>
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
        /* Uploads and determines string signature (len 20 bytes)
           Sanitizes malware name input
           Sanitizes the signature prior to use */
        if (isset($_POST['name']) && $_FILES) {
            $name = mysql_entities_fix_string($conn, $_POST['name']);

            $fail .= validate_name($name);

            if ($fail == "") {
                $n = $_FILES['filename']['name'];
                move_uploaded_file($_FILES['filename']['tmp_name'], 'uploads/'.$n) or die("<p><font color='red'><i>Must upload a file</i></font></p>");

                $ha = fopen('uploads/'.$n, "rb") or die("<p><font color='red'><i>Could not open file</i></font></p>");
                if (flock($ha, LOCK_EX)) {
                    $data = unpack("H20sig", fread($ha, "20"));
                    flock($ha, LOCK_UN);
                }
                fclose($ha);

                unlink('uploads/'.$n) or die("<p><font color='red'><i>Could not delete file</i></font></p>");

                $sig = mysql_entities_fix_string($conn, $data['sig']);

                $query = "INSERT INTO mal (name, sig) VALUES ('$name', '$sig');";

                $result = $conn->query($query);

                if (!$result) {
                    echo "<p><font color='red'><i>INSERT failed: $query<br>" . $conn->error . "</i></font></p><br><br>";
                } else {
                    echo "<p><font color='green'><i>Uploaded Malware signature $name : $sig successfully!</i></font></p><br><br>";
                }
            } else {
                echo "<p><font color=red><i>$fail</i></font></p>";
            }
        }
    } else {
        echo <<< _END
    </head>
    <body>
        <div class=center>
            <h1>
                <form padding: 5; action="auth.php" method="post">
                    <input type="hidden" name="siglogin">
                    You must be <button type="submit">logged in</button> to view this page!
                </form>
            </h1>
_END;
    }

    echo "</div></body></html>";

    $conn->close();

    function validate_name($field)
    {
        if ($field == "") {
            return "No malware name was entered.<br>";
        } elseif (preg_match("/[^a-zA-Z0-9_-]/", $field)) {
            return "Only A-Z, a-z, 0-9, - and _ allowed in malware names.<br>";
        }
        return "";
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
        <div class='center'><form action="auth.php" method="post" enctype='multipart/form-data'>Error authenticating user: please <button type="submit">login</button> again!
</form></div></body></html>
_END;
    }
?>
