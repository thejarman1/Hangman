<?php
    session_start();
    echo "Logging In...";
    include("connectioninfo.php");
    $user = $_POST["Username"];
    $pass = $_POST["Password"];

    $sql = "SELECT Salt, Hash FROM User WHERE Username = '$user'";
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    //get the salt, and the hashed password from the db
    $result = $conn->query($sql);

    //if there are results, then get the values
    if( $result->num_rows > 0) 
    {
        $row = $result->fetch_assoc();
		$salt = $row["Salt"];
        $hash = $row["Hash"];
        //use sha256, hash the pass + salt
        $test = hash("sha256", $pass . $salt);
       
        // If the passwords match, log in
        if($test === $hash) {
            $sql = "SELECT Word, Length FROM Wordlist ORDER BY RAND() LIMIT 1;";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $word = $row["Word"];
            $length = $row["Length"];
            $_SESSION["User"] = $user;
            $_SESSION["RightGuesses"] = 0;
            $_SESSION["LettersWrong"] = " ";
            $_SESSION["WordArray"] = array();
            for($i = 0; $i < $length; $i++) {
                $_SESSION["WordArray"][$i] .= "_";
            }
            $_SESSION["Word"] = $word;
            $_SESSION["Length"] = $length;
            $_SESSION["LoggedIn"] = TRUE;
            $_SESSION["WrongGuesses"] = 0;
            header("location: Hangman.php");
            exit;
        }
        else {
            $_SESSION["PassError"] = TRUE;
            header("location: Welcome.php");
            exit;
        }
    }
    //if there arent any, then the user doesn't exist, and we need to make a new account.
    else {
        $_SESSION["UserError"] = TRUE;
        header("location: Welcome.php");
        exit;
    }
    $conn->close();
?>