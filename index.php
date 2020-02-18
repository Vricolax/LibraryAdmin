<?php
	if (!isset($_SESSION))
		session_start();
	
	//Include external files
	include_once "php/database.php";
	
	//Initialize DB object and set the database
	$db = new Database();
	$db->InitializeDatabase();
	
	//Connect to the database
	$conn = mysqli_connect("localhost","root","","library");
	
	//Redirect user if he is already connected
	//in order to prevent the user to come back in this page after loggin in
	if(isset($_SESSION['username']))
		header("location: home.php");
	
	//Variable storing the login error message
	$loginErrorMessage='';
	
	//Get values from the login form
	if(!empty($_POST['user']) && !empty($_POST['pass']))
	{
		$username = $_POST['user'];
		$password = $_POST['pass'];
		
		//Retrieve data from the database
		$sql = "SELECT * FROM employees WHERE username = '$username' AND password = '$password'";
		$result = mysqli_query($conn, $sql);
		$row = mysqli_fetch_array($result);
		
		//Check if the user exists in the database
		if($row['username'] == $username && $row['password'] == $password)
		{
			$_SESSION['username'] = $row['username'];
			header('location:home.php');
			$loginErrorMessage="";
		}
		else
		{
			$loginErrorMessage = "<h3><br><font color='red'>Username or Password is wrong</font></br></h3>";
		}
	}
	else if(empty($POST['user']) && empty($_POST['pass']))
		$loginErrorMessage="";
?>

<!DOCTYPE html>

<html >
	<head>
	<link rel="stylesheet" type="text/css" href="style.css">
	  <meta charset="UTF-8">
	  <title>Library Login</title>
	  
	  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">

	  <link rel='stylesheet prefetch' href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900|RobotoDraft:400,100,300,500,700,900'>
	<link rel='stylesheet prefetch' href='http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>

		  <link rel="stylesheet" href="css/style.css">
	</head>

	<body background="images/background.jpg">

	<div class="pen-title">
	  <h1><b>Library Login Form</b></h1>
	</div>  

	<div class="module form-module">
	  <div><i style="width:800px;"></i>
		
	  </div>
	  <div class="form">
		<!--  Login -->
		<h2>Login to your account</h2>
		<h3 style="background-color: #66ff66;"><b>Info</b> Administrator:<br> username: <i>admin</i> password: <i>admin</i></h3>
		<?php echo $loginErrorMessage; ?><br><br>
		<form action="index.php" method="POST">
		  <input type="text" placeholder="username" name="user" required/>
		  <input type="password" placeholder="password" name="pass" required/>
		  <button>Login</button>
		</form>
	  </div>
	</div>
	</body>
</html>

