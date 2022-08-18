<?php 
    session_start();
    include("connectioninfo.php");
    
    if($_SESSION["LoggedIn"] === FALSE || $_SESSION["LoggedIn"] == NULL || $_SESSION["Length"] == 0 || $_SESSION["Length"] == NULL) {
        $_SESSION["LoggedIn"] = FALSE;
        header("location: Welcome.php");
    }
    $length = intval($_SESSION["Length"]);
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) 
    {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM HighScores WHERE Length = $length ORDER BY Guesses LIMIT 10";
    $result = $conn->query($sql);
    $rows = array();
    if($result->num_rows > 0) {
        $i = 0;
        while($row = $result->fetch_assoc()){
            $rows[$i++] = "<td>" . $row["Username"] . "</td>" . "<td>" . $row["Word"] . "</td>" . "<td>" . $row["Guesses"] . "</td>"; 
        }
    }
	
	function resetSession() {
     session_unset();
     session_destroy();
     header("location:Welcome.php");
	}
?>

<!DOCTYPE html>
<html>
    <head>
        <!-- Bootstrap -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<title>High Scores</title>
   <style>
   body{
    background-color: #080710;
	color: white;
}

table{
	background-color: rgba(255,255,255,0.13);
}

th,td{
	color: white;
}
.btn-danger, .btn-success {
    background-color: #080710;
}
   </style>
   </head>
<body>
<div class="container mx-auto-mt-4">
    <table class="table mx-auto border mt-4 w-50">
        <h1 class = "text-center mt-4 mb-4"> High Scores </h1>
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">User</th>
                <th scope="col">Word Guessed</th>
                <th scope="col">Total Guesses</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($rows) > 0) {
                
                for($i = 0; $i < count($rows); $i++) {
                    $num = $i + 1;
                    echo "<tr>" . "<th scope=\"row\">" . "$num" . "</th>" . $rows[$i] . "</tr>";
                }
            }
            else {
                echo "no scores...";
            }
            ?>

        </tbody>
    </table>

    <div class="container mx-auto m-4">
        <h1 class = "text-center"> 
            <?php echo $_SESSION["endGameMsg"]; ?> 
        </h1>
        <h4 class = "text-center"> 
            The word was "<?php echo $_SESSION["Word"] . "\".<br>"; ?> 
        </h4>
        <p class = "text-center mt-4 mb-4"> 
        </p>
        <div class="w-50 text-center mx-auto">
        <a class = "btn btn-success mr-4" href="PlayAgain.php">Play Again </a>

        <a class="btn btn-danger" href="?sendcode2=true" role="button">Logout</a>
            <?php 
            if(isset($_GET['sendcode2'])) {
                resetSession();
            }        
            ?>
            </div>
    </div>
</div>
</body>
</html>