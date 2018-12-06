<?php
    require_once 'login.php';
    $connection = new mysqli($hn, $un, $pw, $db);
    if ($connection->connect_error) die($connection->connect_error);

    echo <<< _END
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
        <title>Malware Signature Upload</title>
    </head>
_END;

    session_start();
    if (isset($_SESSION['username']))
    {
        echo <<< _END
    <body>
        <div class="center">
            <h1>Signature Upload</h1>
            <form margin: 0 auto; action="main.php" method="post" enctype='multipart/form-data'>
                Select File <input type='file' name='malsig' size='10'>
                <input type='submit' value='Upload'>
            </form>
            <h4><a href="dismal.php">File Upload</a></h4>
            <form action="query.php" method="post">
                <input type="hidden" name="print" value="yes">
                <a href="dismal.php">Logout</a>
            </form>
        </div>
    </body>
</html>
_END;
    } else {
        echo <<< _END
    <body>
        <h1>You must be <a href='auth.php'>logged in</a> to view this page!</h1>
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
?>