<?php
	session_start();
	require "config.php";
	
	/*$data = json_decode(file_get_contents("php://input"));
		print_r($jjson);
		echo json_last_error();
		echo $data; */
	
	//Har någon tryckt på kanppen
	if (isset($_POST['submitReg'])){
		
		//Sätter variabler för datan från registeringen.
		$user = $_POST['username'];
		$pass = $_POST['password'];
		$epost = $_POST['email'];
		$tel = $_POST['tel'];
		$fornamn = $_POST['fornamn'];
		$efternamn = $_POST['efternamn'];
		
		//Tar bort blackspace
		foreach($_POST as $key => $val){
			$_POST[$key] = trim($val);
		}
		
		//Kolla efter tomma fält. Om tomma: stopa koden.
		if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email']) || empty($_POST['fornamn']) || empty($_POST['efternamn'])) {
			die("Vänligen fyll i alla fälten");
		} 
		
		// Kolla om användarnamnet är upptaget
		$sql = "SELECT anvandarnamn FROM inlogg WHERE anvandarnamn = :useruo";
		$kollaUser = $pdo->prepare($sql);
		$kollaUser->execute (array(':useruo' => $user)); 
		$CheckTwoUser = $kollaUser->fetch(PDO::FETCH_ASSOC);
		echo $CheckTwoUser;
		
		// Kolla om mejlen är upptagen
		$sql2 = "SELECT email FROM inlogg WHERE email = :mailuo";
		$kollaMail = $pdo->prepare($sql2);
		$kollaMail->execute (array(':mailuo' => $epost)); 
		$CheckTwoMail = $kollaMail->fetch(PDO::FETCH_ASSOC);
		echo $CheckTwoMail;
		
		//Om användarnamnet är upptaget: stopa koden.
		if ($CheckTwoUser != NULL) {
			die("Användarnamnet är redan upptaget.");
		}
		
		//Om mejlen är upptagen: stopa koden.
		if ($CheckTwoMail != NULL) {
			die("E-posten är redan upptagen.");
		}
		
		//Inga fel?!? Let's goooooooooooo!!!!!!
		
		//Skapar hashed lössenord med default inställnigar.
		$hashpass = password_hash($pass, PASSWORD_DEFAULT);
		
		//släng in inlogginfo i db
		$sql3 = "INSERT INTO inlogg (anvandarnamn, fornamn, efternamn, email, lossenord, tel)
            VALUES(:useruo, :foruo, :efteruo, :mailuo, :lossuo, :teluo)";
		$inlogg_intoDb = $pdo->prepare($sql3);
		$inlogg_intoDb->execute (array(':useruo' => $user, ':foruo' => $fornamn, ':efteruo' => $efternamn, ':mailuo' => $epost, ':lossuo' => $hashpass, ':teluo' => $tel)); 
		
		//Släng in annan info i db.
		
		//starta session och logga in.
		$sql4 = "SELECT id FROM inlogg WHERE anvandarnamn = :useruo";
		$loggin = $pdo->prepare($sql4);
		$loggin->execute (array(':useruo' => $user)); 
		$id = $loggin->fetch(PDO::FETCH_ASSOC);
		
		//Om inloggnig misslyckas
		if ($loggin == NULL) {
			die("Oops, något gick fel! Vänligen kontakta suport.");
		}
		
		//Startar session koplat till användarnas id och anvndarnamn från DB!
		
		$_SESSION['id'] = $id['id'];
		$_SESSION['anvandarnamn'] = $user;
		
	}
?>

<html>
<head></head>
<body>
	<form action="register_v2.php" method="post">
	<label>Namn</label>
	<input type="text" name="username">
	<label>Lössenord</label>
	<input type="password" name="password">
	<label>E-mail</label>
	<input type="text" name="email">
	<label>Telefonnummer</label>
	<input type="text" name="tel">
	<label>Förnamn</label>
	<input type="text" name="fornamn">
	<label>Efternamn</label>
	<input type="text" name="efternamn">
	<input type="submit" name="submitReg" value="Logga in">
</body>
</html>
