<?php
session_start();
if (isset($_SESSION['use'])) {
    $username = $_SESSION['use'];
    $User_ID = $_SESSION['userid'];
} else {
    header("location: index.php");
}
?>


<?php
$getName = $_GET['name'];
?>

<html>
<head>
    <link href="Style_Sheet_Secure.css" rel='stylesheet' type='text/css'/>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab" rel="stylesheet">
</head>
<body>
<table id="Dtable">
    <?php

   include 'db_credentials.php';


    $conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT 
s.Stock_ID, 
s.Stock_Name, 
s.Amount, 
s.Price, 
s.Last_Modified, 
u.Name FROM Stock AS s
INNER JOIN Users AS u
ON s.Modified_by = u.User_ID
WHERE s.Stock_Name LIKE ?";


    if ($getName == "*") {
        $getName = "%";
    } else {
        $getName = "%" . $getName . "%";
    }

    $paramTypes = "s";
    $params = array();
    $params[] = &$getName;

    if (isset($_GET["id"])) {
        $paramTypes .= "i";
        $params[] = &$_GET["id"];
        $query .= " AND s.Stock_ID = ?";
    }

    if (isset($_GET["quantity"])) {
        $paramTypes .= "i";
        $params[] = &$_GET["quantity"];
        $query .= " AND s.Amount = ?";
    }

    if (isset($_GET["price"])) {
        $paramTypes .= "i";
        $params[] = &$_GET["price"];
        $query .= " AND s.Price = ?";
    }

    if (isset($_GET["date"])) {
        $paramTypes .= "i";
        $params[] = &$_GET["date"];
        $query .= " AND date_format(s.Last_Modified, '%Y-%m-%d') = ?";
    }

    if (isset($_GET["by"])) {
        $paramTypes .= "i";
        $params[] = &$_GET["by"];
        $query .= " AND u.Name = ?";
    }

    $getVal = $conn->prepare($query);
    call_user_func_array(array($getVal, "bind_param"), array_merge(array($paramTypes), $params));


    $getVal->bind_result($s_ID, $s_Name, $s_Amount, $s_Price, $s_Moddate, $s_Modby);
    $getVal->execute();
    while ($getVal->fetch()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($s_ID, ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($s_Name, ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td><input type='number' name='changeQuant" . htmlspecialchars($s_ID, ENT_QUOTES, 'UTF-8') . "' value='" . htmlspecialchars($s_Amount, ENT_QUOTES, 'UTF-8') . "' min='0'></td>";
        echo "<td>" . htmlspecialchars($s_Price, ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($s_Moddate, ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($s_Modby, ENT_QUOTES, 'UTF-8') . "</td>";
    }
    ?>
</table>
</body>
</html>