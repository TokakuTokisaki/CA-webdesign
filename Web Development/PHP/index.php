<?php
include 'db_credentials.php';
//Connects to the database
$conn = new mysqli($dbservername, $dbusername, $dbpassword, "ECM1417");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
//This logs the user into the website as long as the username and password are correct
    if(isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $loginst = $conn->prepare("SELECT User_ID, HashP FROM Users WHERE Username = ?;");
        $loginst->bind_param("s", $username);
        $loginst->execute();
        $loginst->bind_result($UserId, $HashPass);
        $loginst->fetch();
        $loginst->close();
        $conn->close();

        if (password_verify($password, $HashPass)) {
            session_start();
            $_SESSION['use'] = $username;
            $_SESSION['userid'] = $UserId;
            header('location: secure.php');
        } else {
            echo "invalid Username or Password";
        }
    }
?>

<html>
    <head>
        <title>Login Page</title>
        <link href="Style_Sheet.css" rel='stylesheet' type='text/css'/>
        <link href="https://fonts.googleapis.com/css?family=Roboto+Slab" rel="stylesheet">
    </head>

    <body>
    <div id="divtag" align="center">
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
            Username: <input class="signIn" id="User" type="text" name="username">
            <br/>
            Password: <input class="signIn" id="Pass" type="password" name="password">
            <br/>
            <input class="signIn" id="Sub" type="submit" name="login" value="LOGIN">
        </form>
    </div>
    </body>
</html>