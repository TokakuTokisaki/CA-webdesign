<?php
session_start();
//Checks whether user is logged in and redirects them to the login page if they are not
if (isset($_SESSION['use'])) {
    $username = $_SESSION['use'];
    $User_ID = $_SESSION['userid'];
} else {
    header("location: index.php");
}

//If there is no token for the html forms this creates one
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

$token = $_SESSION['token'];

//Gets database credentials
include 'db_credentials.php';

//Connects to the database
$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Handles a post request for adding stock
if (isset($_POST['addstock'])) {
    if ($_POST['token'] == $token) {
        $S_Name = $_POST['S_Name'];
        $S_Quant = $_POST['S_Quant'];
        $S_Price = $_POST['S_Price'];

        $addstock = $conn->prepare("INSERT INTO Stock (Stock_Name, Amount, Price, Modified_by) VALUES (?, ?, ?, ?);");
        $addstock->bind_param("sidi", $S_Name, $S_Quant, $S_Price, $User_ID);
        $addstock->execute();
        $addstock->close();
    }
}

//Handles a post request for changing the quantity of stock
if (isset($_POST['changeQuant'])) {
    if ($_POST['token'] == $token) {
        $idStmnt = $conn->prepare("SELECT Stock_ID FROM Stock;");
        $idStmnt->bind_result($inputID);
        $idStmnt->execute();
        $idarray = array();

        //Gets a list of the stock IDs
        while ($idStmnt->fetch()) {
            array_push($idarray, $inputID);
        }

        $idStmnt->close();

        //Changes the amount of stock
        foreach ($idarray as &$inputID) {
            if (isset($_POST['changeQuant' . $inputID])) {
                $changeStmnt = $conn->prepare("UPDATE Stock SET Amount = ?, Modified_by = ? WHERE Stock_ID = ?;");
                $changeStmnt->bind_param("iii", $inputAmount, $User_ID, $inputID);

                $inputAmount = $_POST['changeQuant' . $inputID];
                $status = $changeStmnt->execute();
                if ($status === false) {
                    trigger_error($changeStmnt->error, E_USER_ERROR);
                }
                $changeStmnt->close();
            }
        }
    }
}

//Query for retrieving stock information
$sql = "SELECT
s.Stock_ID,                 
s.Stock_Name,               
s.Amount,                   
s.Price,                    
s.Last_Modified,            
u.Name FROM Stock AS s      
INNER JOIN Users AS u       
ON s.Modified_by = u.User_ID";
$result = $conn->query($sql);
?>
<html>
<head>
    <script src="search.js"></script>
    <link href="Style_Sheet_Secure.css" rel='stylesheet' type='text/css'/>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab" rel="stylesheet">
</head>
<p>
<p><input class="whole" type="button" value="LOG OUT" onclick="window.location.href = 'logout.php'"></p>
<!-- Specify that the form should be sent to the current -->
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
    <!-- To prevent cross site forgery this puts a generated code into the input type which is checked so only the
    logged in person can make the post request and they can only make it on this site -->
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <table>
        <tr>
            <th>Stock Name</th>
            <th>Quantity</th>
            <th>Price</th>
        </tr>
        <tr>
            <td><input class="whole" type="text" name="S_Name"></td>
            <td><input class="whole" type="number" name="S_Quant" min="0" value="0"></td>
            <td><input class="whole" type="number" name="S_Price" min="0" value="0" step="0.01"></td>
            <td><input class="whole" type="submit" name="addstock" value="Add Stock"></td>
        </tr>
    </table>
</form>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <p><input class="whole" type="submit" name="changeQuant" value="Change Amount"> <input class="whole" type="reset"
                                                                                           value="Reset"></p>
    <table id="Headtable">
        <tr>
            <th>Stock ID</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Last Modified</th>
            <th>Modified by</th>
        </tr>
        <tr>
            <th><input class="whole" id="searchId" type="number" placeholder="ID..." min="0" onkeyup="search()"></th>
            <th><input class="whole" id="searchName" type="text" placeholder="Name..." onkeyup="search()"></th>
            <th><input class="whole" id="searchQuant" type="number" placeholder="Quantity..." min="0"
                       onkeyup="search()"></th>
            <th><input class="whole" id="searchPrice" type="number" placeholder="Price..." min="0" onkeyup="search()">
            </th>
            <th><input class="whole" id="searchDate" type="date" placeholder="Date..." onchange="search()"></th>
            <th><input class="whole" id="searchUid" type="text" placeholder="User Name..." min="0" onkeyup="search()">
            </th>
        </tr>
    </table>
    <div id="dataTable">
        <table id="Dtable">
            <?php
            //Creates a table based off the rows of information from the query
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["Stock_ID"], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . $row["Stock_Name"] . "</td>";
                    echo "<td><input class='whole' type='number' name='changeQuant" . htmlspecialchars($row["Stock_ID"], ENT_QUOTES, 'UTD-8') . "' value='" . htmlspecialchars($row["Amount"], ENT_QUOTES, 'UTF-8') . "' min='0'></td>";
                    echo "<td>" . htmlspecialchars($row["Price"], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Last_Modified"], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row["Name"], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "0 Results";
            }
            $conn->close();
            ?></table>
    </div>
</form>
</body>
</html>
