<?php
    session_start();
    include("connectioninfo.php");

    $_SESSION["LoggedIn"] = FALSE;
    $user = $_POST["SignupUsername"];
    $pass = $_POST["SignupPassword"];
    $passConf = $_POST["PasswordConf"];

    //if this is our first time visiting this page
    if($user != NULL && $pass != NULL && $passConf != NULL) 
    { 
        //establish connection, and error check.
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) 
        {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM User WHERE Username = '$user'";
        $result = $conn->query($sql);

        //if there is a result, then the username
        //already exists
        if( $result->num_rows > 0) 
        {
            $_SESSION["UserError"] = TRUE;
        }
        //if the password matches the confirm password,
        //and the length requirement is met
        else if($pass == $passConf) 
        {
            //generate a random salt
            $randomText= md5(uniqid(rand(), TRUE));
            $salt=  substr($randomText, 0, 3);
            //hash the password + salt
            $hash = hash("sha256", $pass . $salt);

            $sql = "INSERT INTO User (Username, Hash, Salt) 
                        Values('$user', '$hash', '$salt')";
            //store the hash, and salt in the DB
            //then kick "LoggedIn" to true.
            if ($conn->query($sql) === TRUE) 
            {
                $sql = 
                "SELECT Word, Length FROM Wordlist ORDER BY RAND() LIMIT 1;";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $word = $row["Word"];
                $length = $row["Length"];
                $_SESSION["Word"] = $word;
                $_SESSION["WordArray"] = array();
                for($i = 0; $i < $length; $i++) {
                    $_SESSION["WordArray"][$i] .= "_";
                }
                $_SESSION["LettersWrong"] = " ";
                $_SESSION["User"] = $user;
                $_SESSION["Length"] = $length;
                $_SESSION["LoggedIn"] = TRUE;
                $_SESSION["RightGuesses"] = 0;
                $_SESSION["WrongGuesses"] = 0;
                //redirect to game
                header("location: Hangman.php");
                exit;
            } 
            //otherwise we have an error
            else 
            {
            echo "Error: " . $sql . "<br>" . $conn->error;
            }
            //close the connection
            $conn->close();
        }
        //if we get here, there is a password error
        else 
        {
            $_SESSION["PassError"] = TRUE;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Create User</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <!--Stylesheet-->
    <style media="screen">
      *,
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
.background .shape{
    height: 200px;
    width: 200px;
    position: absolute;
    border-radius: 50%;
}
.shape:first-child{
    background: linear-gradient(
        #1845ad,
        #23a2f6
    );
    left: -80px;
    top: -80px;
}
.shape:last-child{
    background: linear-gradient(
        to right,
        #ff512f,
        #f09819
    );
    right: -30px;
    bottom: -80px;
}
div.form{
    height: 575px;
    width: 400px;
    background-color: rgba(255,255,255,0.13);
    position: absolute;
    transform: translate(-50%,-50%);
    top: 50%;
    left: 50%;
    border-radius: 10px;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.1);
    box-shadow: 0 0 40px rgba(8,7,16,0.6);
    padding: 50px 35px;
}
form *{
    font-family: 'Poppins',sans-serif;
    color: #ffffff;
    letter-spacing: 0.5px;
    outline: none;
    border: none;
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
    width: 100%;
    background-color: rgba(255,255,255,0.07);
    border-radius: 3px;
    padding: 0 10px;
    margin-top: 8px;
    font-size: 14px;
    font-weight: 300;
}
::placeholder{
    color: #e5e5e5;
}
button{
    margin-top: 40px;
    width: 100%;
    background-color: #ffffff;
    color: #080710;
    padding: 15px 0;
    font-size: 18px;
    font-weight: 600;
    border-radius: 5px;
    cursor: pointer;
}

    </style>
</head>
<body>
	<div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
   <div class="form">
    <form action="NewUser.php" method="post">
        <h3>Create Account</h3>

        <label for="SignupUsername">Username</label>
        <input type="text" placeholder="Username" name="SignupUsername" id="SignupUsername" required>
		
		<?php if($_SESSION["UserError"]) : ?>
            <span>Username is already taken. Enter a different Username</span>
        <?php $_SESSION["UserError"] = FALSE; ?>
    <?php endif; ?>
		
        <label for="SignupPassword">Password</label>
        <input type="password" placeholder="Password" name="SignupPassword" id="SignupPassword" required>
		
		<label for="PasswordConf">Confirm Password</label>
        <input type="password" placeholder="Confirm Password" name="PasswordConf" id="PasswordConf" required>
		
		<?php if($_SESSION["PassError"]) : ?>
            <span>Passwords Do Not Match. Try Again</span>
        <?php $_SESSION["PassError"] = FALSE; ?>
    <?php endif; ?>
		
        <button class="btn btn-primary" type = "submit">Create</button>
    </form>
	
	</div>
</body>
</html>