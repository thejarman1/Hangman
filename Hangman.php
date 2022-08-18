<?php
session_start();
include("connectioninfo.php");

if($_SESSION["LoggedIn"] === FALSE || $_SESSION["LoggedIn"] == NULL) {
    header("location: Welcome.php");
}

$user = $_SESSION["User"];
$space = "&nbsp;";
$wordCount = intval($_SESSION["Length"]);
$word = $_SESSION["Word"];
$wrongGuesses = intval($_SESSION["WrongGuesses"]);
$rightGuesses = intval($_SESSION["RightGuesses"]);
$test= substr($_POST["Guess"], 0, 1);
$_SESSION["rightGuess"] = FALSE;
$rightGuess = FALSE;

if(ctype_alpha($test) === FALSE && isset($_POST["Guess"])) {
    $guess = "~";
    header("location: Hangman.php");
}
else {
    $guess = $test;
}
if($guess === NULL || $_SESSION["WrongGuesses"] === NULL) {
    $_SESSION["WrongGuesses"] = 0;
}

$offset = 0;
    while(($pos = stripos($_SESSION["Word"], $guess, $offset))!== FALSE) {
        if(stripos($_SESSION["LettersWrong"], $guess) !== FALSE || $guess == "~"){
            break;
        }
        else if(exists($_SESSION["WordArray"], $guess)) {
            $_SESSION["rightGuess"] = TRUE;
            $rightGuess = TRUE;
            break;
        }
        $_SESSION["WordArray"][$pos] = $guess;
        $offset = $pos+1;
        $_SESSION["rightGuess"] = TRUE;
        $rightGuess = TRUE;
        $_SESSION["RightGuesses"]++;
        $rightGuesses++;
    }

if($_SESSION["RightGuesses"] == $wordCount) {
 
    $_SESSION["endGameMsg"] = "You Won!";
    $_SESSION["LoggedIn"] = FALSE;

    $totalGuesses = $rightGuesses + $wrongGuesses;

    $sql = "INSERT INTO HighScores (Word, Length, Guesses, Username)
            VALUES('$word', $wordCount, $totalGuesses, '$user')";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) 
    {
        die("Connection failed: " . $conn->connect_error);
    }
    if($conn->query($sql)) 
    {
        $conn->close();
        $_SESSION["LoggedIn"] = TRUE;
        header("location: HighScores.php");
    }
    else 
    {
        $conn->close(); 
        echo "error...";
    }
}

if($_SESSION["WrongGuesses"] == 11) {
    $_SESSION["endGameMsg"] = "You Lost!";
    $_SESSION["LoggedIn"] = TRUE;
    if($wrongGuesses < 0) {
        $wrongGuesses = 0;
    }
    if($rightGuesses < 0) {
        $rightGuesses = 0;
    }
    $totalGuesses = $rightGuesses + $wrongGuesses;

    $sql = "INSERT INTO HighScores (Word, Count, Guesses, Username)
            VALUES('$word', $wordCount, $totalGuesses, '$user')";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) 
    {
        die("Connection failed: " . $conn->connect_error);
    }
    $result = $conn->query($sql);
    $_SESSION["LoggedIn"] = True;
    $conn->close();
    header("location: HighScores.php");
}

function exists(&$arr, $val) {
    foreach($arr as $i) {
        if(stripos($i, $val) !== FALSE) {
            return TRUE;
        }
    }
        return FALSE;
}

$hang = array(

'<h3><br><br><br><br><br><br>=========</h3> ',

'<h3>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+---+<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'========='.
'</h3>',

'<h3>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+---+<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;O&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'========='.
'</h3>',

'<h3>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+---+<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;O&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'========='.
'</h3>',

'<h3>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+---+<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;O&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;|\&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'========='.
'</h3>',

'<h3>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+---+<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;O&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp/|\&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'========='.
'</h3>',

'<h3>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+---+<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;O&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp/|\&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'========='.
'</h3>',

'<h3>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+---+<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;O&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;/|\&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;\&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<br>'.
'========='.
'</h3>',
);

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Hangman Game</title>
		<link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <!--Stylesheet-->
    <style media="screen">
		
*:before,
*:after{
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}
body{
    background-color: #080710;
}
.background{
    width: 430px;
    height: 520px;
    position: absolute;
    transform: translate(-50%,-50%);
    left: 50%;
    top: 50%;
}

div {
	color: white;
}

h3 {
	font-size: 30px;
}

div.word {
	text-align: center;
}

div.form {
	width:400px;
	margin: 0 auto;
}

div form{
    height: 175px;
    width: 350px;
    background-color: rgba(255,255,255,0.13);
    position: absolute;
    border-radius: 10px;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.1);
    box-shadow: 0 0 40px rgba(8,7,16,0.6);
    padding: 0px 35px;
	text-align: center;
}
form *{
    font-family: 'Poppins',sans-serif;
    color: #ffffff;
    letter-spacing: 0.5px;
    outline: none;
    border: none;
}

h1 {
	font-family: 'Poppins',sans-serif;
    color: #ffffff;
    letter-spacing: 0.5px;
    text-align: center;
	font-size: 3em;
}

p {
	margin-top: 10px;
}

form h1{
	font-size: 40px;
    font-weight: 500;
    line-height: 42px;
    text-align: center;
	margin-bottom: 15px;
}	
	form h3{
    font-size: 32px;
    font-weight: 500;
    line-height: 42px;
    text-align: center;
}

label{
    display: block;
    margin-top: 20px;
    font-size: 16px;
    font-weight: 500;
}

span{
	margin-top: 15px;
	margin-left: 20px;
	display: block;
	font-size: 14px;
	font-weight: 500;
	color: red;
}

input{
    display: block;
    height: 50px;
    width: 20%;
    background-color: rgba(255,255,255,0.07);
    border-radius: 3px;
    padding: 0 10px;
    margin: 15px auto;
    font-size: 16px;
    font-weight: 300;
	text-align: center;
}

button{
    width: 30%;
    background-color: #ffffff;
    color: black;
    padding: 12px 0;
    font-size: 14px;
    font-weight: 600;
    border-radius: 5px;
    cursor: pointer;
} 
			
		
	</style>
    </head>
    <body>
    <div>
        <h1> Hangman</h1>
        <hr>
        <div class="guess">
            <div class="form">
                <form align="center" action = "Hangman.php" method = "post">
                        <label for="Guess">Guess a Letter: </label>
                        <input align="center" type="text" name = "Guess" id="Guess" >
                        <button class="btn btn-primary"  type = "submit">Submit</button>
                </form>
            </div>
			<div>
                <?php 
                    if($_SESSION["rightGuess"] !== TRUE) {
                        if(isset($_POST["Guess"]) && stripos($_SESSION["LettersWrong"], $guess) === FALSE && $guess != "~") {
                            $_SESSION["LettersWrong"] .=  $guess . " , ";
                            $_SESSION["WrongGuesses"]++;
                        }
                    }
					if ($_SESSION["WrongGuesses"] <= 7) {
                    echo $hang[$_SESSION["WrongGuesses"]];
                    echo "Wrong Guesses: " . $_SESSION["LettersWrong"];
					} else {
						echo $hang[7];
						echo "Wrong Guesses: " . $_SESSION["LettersWrong"];
					}
                    ?>
            </div>
        </div>
        <hr>
        <h1 class = "text-center mt-4 mb-4"> 
            <?php 

                echo implode("&nbsp;&nbsp",$_SESSION["WordArray"]) . "<br>"
            ?>
        </h1>
        <div class="word">Guess the<?php echo $space . $wordCount . $space; ?> letter word</div>
        <br>
        <hr>
    </div>
    </body>
</html>