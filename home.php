<?php
	if(!isset($_SESSION))
		session_start();
	
	//Include external files
	include_once "php/database.php";
	include_once "php/employee.php";
	//include "php/lookForBooks.php";
	
	//Initialize DB object and set the database
	$db = new Database();

	
	//Create DB connection
	$conn = mysqli_connect("localhost","root","","library");
	
	//Initialize employee object
	$emp = new Employee;
	
	$emp_error="";
		
	////Validate Added/Deleted Elements
	$emp_added = 0;
	
	//Variable storing a permission error trigger for the simple user account (it will be user in js)
	$emp_add_error =0;
	
	//Add new Employee to the DB
	if(!empty($_GET['fname']) && !empty($_GET['lname']))
	{
		if($_SESSION['username'] == "admin")
		{
			$first_name = $_GET['fname'];
			$last_name = $_GET['lname'];
			$username = $_GET['username'];
			$password = $_GET['password'];
			
			$conn = mysqli_connect('localhost','root','','library');
			$_sql = "SELECT * FROM employees WHERE firstName = '$first_name' AND lastName = '$last_name'";
			$_result = mysqli_query($conn,$_sql);
			$row_emp = mysqli_fetch_array($_result);
			
			//Check if employee already exists
			if($row_emp['firstName'] == $first_name && $row_emp['lastName'] == $last_name)
			{
				$emp_error = "<font size='5' color='red'>Warning: This employee already exists!</font>";
				$emp_added=0;
			}
			else
			{
				$emp->_constructor($first_name, $last_name);
				
				$db->AddEmployee($emp->GetFirstName(), $emp->GetLastName(), $username, $password);
				$emp_added = 1;
				//header("location: index.php");
			}
		}
		else
			$emp_add_error = 1;
	}
	
	//for testing purposes
	$last_id_emp = -1;
	$last_id_cust = -1;
	$last_id_book = -1;
	
	//Get the total number of books
	$totalBooks = 0;
	$sql = "SELECT * FROM books ORDER BY id";
	$result_books_total = mysqli_query($conn, $sql) or die("no books found");
	while($row = mysqli_fetch_array($result_books_total))
	{
		$totalBooks += 1;
	}

	//Get all the books from the DB
	$sql_books = "SELECT * FROM books";
	$result_books = mysqli_query($conn, $sql) or die("no books found 2");
	
	//Get the total number of customers
	$totalCusts = 0;
	$sql_customers_total = "SELECT * FROM customers";
	$result_customers_total = mysqli_query($conn, $sql_customers_total) or die("no customers found");
	while($row = mysqli_fetch_array($result_customers_total))
	{
		$totalCusts += 1;
	}
	
	//Get all the customers
	$sql_customers = "SELECT * FROM customers ORDER BY id";
	$result_customers = mysqli_query($conn, $sql_customers) or die("no customers found");

	// query the last row of emps from database
	$sql_emp = "select * from employees order by id desc";
	
	$result1 = mysqli_query($conn, $sql_emp);
	
	// create new emp row
	while($row = mysqli_fetch_array($result1))
	{
		if($last_id_emp == -1)
		{
			$last_id_emp = $row['id'];
		}
	}
		
	//Delete employee
	if(!empty($_GET['deleteEmployee']))
	{
		$emp_id = $_GET['deleteEmployee'];
		$del_emp = "DELETE FROM employees WHERE id= '$emp_id'";
		mysqli_query($conn, $del_emp);
	}
	
	//Prevent the user to come back in this page after logging out
	if(!isset($_SESSION['username']))
		header("location:index.php");
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Library Admin</title>
	<!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
     <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
     <!-- GOOGLE FONTS-->
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
        <!-- TABLE STYLES-->
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
	
	<!-- JQuery-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

</head>
<body>
	<!--<img src="images/background.jpg" style="position:absolute; position:fixed; opacity:0.5; top:0;"></img>-->
    <div id="wrapper">
        <nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0;">

            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="home.php">Library admin</a> 
            </div>
			  <div style="color: white;
padding: 15px 50px 5px 50px;
float: right;
font-size: 16px;"><a href="logout.php?logout=1" class="btn btn-danger square-btn-adjust" >Logout</a>
	</div>
        </nav>   
           <!-- /. NAV TOP  -->
                <nav class="navbar-default navbar-side" role="navigation" style="height:100%;">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
				<li class="text-center">
                    <img src="images/book3.png" class="user-image img-responsive"/>
					</li>
				
					
                    <li>
                        <a class="active-menu"  href="home.php"><i class="fa fa-desktop fa-3x"></i> Dashboard</a>
                    </li>
						   <li>
                        <a   href="orders.php"><i class="glyphicon glyphicon-shopping-cart fa-3x"></i> Customers orders</a>
                    </li>	
                      <li>
                        <a  href="reservations.php"><i class="glyphicon glyphicon-calendar fa-3x"></i> Books Reservations</a>
                    </li>
                    <li>
                        <a  href="management.php"><i class="fa fa-edit fa-3x"></i> Control Panel</a>
                    </li>
                  <li>
                        <a  href="partners.php"><i class="fa fa-sitemap fa-3x"></i> Partners</a>
                    </li>	
                </ul>
               
            </div>
            
        </nav>  
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <h2><b>Admin Dashboard</b></h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
                <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-6">           
			<div class="panel panel-back noti-box">
                <span class="icon-box bg-color-red set-icon">
                   <a href="#bookSection"> <i class="fa fa-book"></i></a>
                </span>
                <div class="text-box" >
                    <p class="main-text" id="TotalBooks"><?php echo $totalBooks; ?><!--1.520 Books--></p>
                    <p class="text-muted">Our stock</p>
                </div>
             </div>
			 
		     </div>
                    <div class="col-md-3 col-sm-6 col-xs-6">           
			<div class="panel panel-back noti-box">
                <span class="icon-box bg-color-green set-icon">
                    <a href="#customerSection"><i class="fa fa-users"></i></a>
                </span>
                <div class="text-box" >
                    <p class="main-text" id="TotalCusts"><?php echo $totalCusts; ?><!--632 Customers--></p>
                    <p class="text-muted">Our customers</p>
                </div>
             </div>
		     </div>
                 <!-- /. ROW  -->
                <hr />                
                <div class="row">
                    
        </div>
		
			<?php	
			if(isset($_SESSION['username']) && $_SESSION['username'] == "admin")
			{
				echo "
                 <!-- /. ROW  -->
                <div class='row' >
                    <div class='col-md-9 col-sm-12 col-xs-12'>
					". $emp_error."
                    <div class='panel panel-default'>
                        <div class='panel-heading'>
                           <h4><b>Our Employees</b></h4>
                        </div>
                        <div class='panel-body'>
                            <div class='table-responsive' style='height:45%; width:100%; overflow-y: auto;'>
                                <table class='table table-striped table-bordered table-hover' >
                                    <thead>
                                        <tr>
                                            <th># id</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Username</th>
                                            <th>Password</th>
											 <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<tr>
											<!-- ADD NEW CUSTOMER-->
											<form action='#' method='GET'>
												<td><b>Add Employee</b></td>
												<td><input required='required' pattern='[a-zA-Z]*' name='fname'></td>
												<td><input required='required' pattern='[a-zA-Z]*' name='lname'></td>
												
												<td><input required='required' name='username'></td>
												<td><input required='required' name='password'></td>
												<td><input type='submit' value='Add' style='background-color:#eeffe6;'></td>
											</form>
											<!-- /ADD NEW CUSTOMER-->
										</tr>
									</tbody>
									<tbody id='all-emps'>
                                        <!-- HERE ARE DISPLAYED THE EMPLOYEES (by ajax)-->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    </div>
                </div>    
				";
			}	
			?>
                 <!-- /. ROW  -->  
				 <div class="row">
					 <div class="col-md-12">
						<div class="panel panel-default" id="customerSection">
							<div class="panel-heading">
								<h4><b>Our Customers</b></h4>
							</div>
							<!-- HEAD customer table-->
							<div class="panel-body">
								<table class="table table-striped table-bordered table-hover" id="dataTables-customers">
									<thead>
										<tr>
											<th style="width:5%;"># id</th>
											<th style="width:45%;">First Name</th>
											<th style="width:45%;">Last Name</th>
										</tr>
									</thead>
									<tbody>
										<?php
											while($row = mysqli_fetch_array($result_customers))
											{
												echo "
													<tr>
														<td style='width:5%'>".$row['id']."</td>
														<td style='width:45%'>".$row['firstName']."</td>
														<td style='width:44%'>".$row['lastName']."</td>
													</tr>
												";
											}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				
				
				<!-- /. ROW  -->  
				 <div class="row">
					 <div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4><b>Our Books</b></h4>
							</div>
							<!-- HEAD books table-->
							<div class="panel-body" id="bookSection">
								<table class="table table-striped table-bordered table-hover" id="dataTables-books">
										<thead>
											<tr>
												<th style="width:5%;"># id</th>
												<th style="width:47%;">Title</th>
												<th style="width:26%;">Author</th>
												<th style="width:22%;">Category</th>
												<th style="width:22%;">Status</th>
											</tr>
										</thead>
										<tbody>
											<?php
												while($row = mysqli_fetch_array($result_books))
												{
													echo "
														<tr>
															<td style='width:5%;'>".$row['id']."</td>
															<td style='width:35%;'>".$row['title']."</td>
															<td style='width:26%;'>".$row['authorName']."</td>
															<td style='width:21%;'>".$row['category']."</td>
															<td style='width:12%;'>".$row['status']."</td>
														</tr>
														";
												}
											?>
										</tbody>
									</table>
							</div>
									
						</div>
					</div>
				</div>
				
				<!-- Google Maps-->
				<h3><b style="position:relative;margin-left:40%;">Our Location</b></h3>
				<div>
					<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3027.531392947441!2d22.93388891566481!3d40.64021524994037!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14a839a6b67cf16b%3A0xca9d257d70e440db!2zzpzOtc-Dzr_Os861zrnOsc66z4wgzprOv867zrvOtc6zzrnOvyDOmM61z4PPg86xzrvOv869zrnOus63z4I!5e0!3m2!1sen!2sus!4v1493155675401" width="100%" height="300" frameborder="0" style="border:solid 1px;" allowfullscreen></iframe>
				</div>
				<!-- /Google Maps-->
				
				<!-- ROW -->
    </div>
             <!-- /. PAGE INNER  -->
            </div>
         <!-- /. PAGE WRAPPER  -->
        </div>
     <!-- /. WRAPPER  -->
    <script src="assets/js/bootstrap.min.js"></script>
	
		<!-- Tables SCRIPTS-->
	<script src="assets/js/dataTables/jquery.dataTables.js"></script>
	<script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
	<script>
		$(document).ready(function () {
			$('#dataTables-customers').dataTable();
		});
		$(document).ready(function () {
			$('#dataTables-books').dataTable();
		});
	</script>
      <!-- CUSTOM SCRIPTS -->
    <script src="assets/js/custom.js"></script>
	
	<script>
		var last_emp_id = "<?php echo $last_id_emp; ?>";
		
		var empadded = "<?php echo $emp_added; ?>";
		var user = "<?php echo $_SESSION['username']; ?>";
		var user_error = "<?php echo $emp_add_error; ?>";
		
		if(empadded == 1)
		{
			alert("Employee Added succesfully");
			window.location.replace("home.php");
		}
		if(user_error == 1)
		{
			alert("You do not have the permission to perform this action! For More information, please contact the Administrator.");
			window.location.replace("home.php");
		}		
		
		$(document).ready(function()
		{
			// call the updateEmployees() function every 0.2 second
			// 200 => integer number in msec
			setInterval(updateEmployees, 200);		
		});
		
		function deleteEmp(empId)
		{
			var emp_id = empId;
			
			if(user == "admin")
			{
				$.ajax({
					
					url: '/library/home.php',
					data:{
						deleteEmployee : emp_id
					},
					type: 'GET',
					success: function(result){
						alert("Employee Deleted sucesfully");
					}
				});
			}
			else
				alert("You do not have the permission to perform this action! For More information, please contact the Administrator.");
		};
		
		//Retrieve all the new employees in the database
		function updateEmployees() 
		{
			$.ajax({
				// url of script to be run
				url: "/library/php/lookForEmps.php/",
				
				// send the last_emp_id as an argument to script
				// for debugging purposes
				data: {
					last_emp_id_known: last_emp_id
				},
				
				// function to be run when script successfully sends data
				success: function( result ) {
					// get the json object of result (de-serialization)
					var obj = JSON.parse(result);
					
					//Clean the emps list in order to display a refreshed one
					$("#all-emps").html('');
					
					// add new emps' content in the emps list (#all-emps table)
					$("#all-emps").prepend(obj['emp']);
				},
				
				// type of request -> POST
				type: "POST"
			});
		};

	</script>
    
   
</body>
</html>
