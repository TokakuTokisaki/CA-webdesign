<?php
//This destroys the session once a user decides to log out
    session_start();
    if (isset($_SESSION['use'])) {
        session_unset();
        session_destroy();
    }
    header("location: index.php")
?>

<html>
    <head>
        <title>Logout</title>
    </head>
    <body>
        <p>You have successfully logged out</p>
    </body>
</html>
