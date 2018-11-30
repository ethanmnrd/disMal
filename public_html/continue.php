<?php
    session_start();

    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $password = $_SESSION['password'];
        $fname = $_SESSION['fname'];
        $lname = $_SESSION['lname'];

        destroy_session_and_data();

        echo "Welcome back $fname.<br>
            Your full name is $fname $lname.<br>
            Your username is '$username' and your password is '$password'.";
    } else {
        echo "Please <a href='auth.php'>click here</a> to log in.";
    }

    function destroy_session_and_data()
    {
        session_start();
        $_SESSION = array();
        setcookie(session_name(), '', time() - 2592000, '/');
        session_destroy();
    }
?>