<?php
	if(!isset($_SESSION))
		session_start();
	
	include_once "php/database.php";
	
	//Connect to the database
	$conn = mysqli_connect('localhost','root','','library');
		
	//Get all the restored books from the DB
	$sql_book = "SELECT * FROM books";
	$result_book = mysqli_query($conn, $sql_book) or die("No book found reservations.php result");
	
	//Get all the cusomers from the DB
	$sql_cust = "SELECT * FROM customers";
	$result_cust = mysqli_query($conn, $sql_cust) or die("No customer found reservations.php result");

	// Initialize DB object
	$db = new Database();
	
	//Store error messages
	$reserv_error="";
	
	//Validate added reservation
	$reserve_added = 0;
	
	if(!empty($_GET['selection_cust']) && !empty($_GET['selection_book']) && !empty($_GET['reserve']) && !empty($_GET['restore']))
	{
		$cust =$_GET['selection_cust'];
		$book =$_GET['selection_book'];
		$reserve =$_GET['reserve'];
		$restore =$_GET['restore'];
		
		$sql = "SELECT * FROM reservations 
				WHERE custID = '$cust' AND bookID = '$book'";
		$sql2 = "SELECT * FROM reservations
				WHERE bookID = '$book'";
		
		//RESERVATION EXISTS
		$validate_reserve = mysqli_query($conn, $sql);
		$reservation = mysqli_fetch_array($validate_reserve);
		
		//BOOK IS AVAILABLE
		$validate_availability = mysqli_query($conn, $sql2);
		$availability = mysqli_fetch_array($validate_availability);
		
		//Check if reservation exists
		if($reservation['custID'] == $cust && $reservation['bookID'] == $book)
		{
			$reserv_error = "<font size='5' color='red'>Warning: This reservation already exists! <br>Reservation Date: ".$reservation['reservationDate'].", Restoration Date: ".$reservation['restorationDate']."</font>";
		}
		//Check if the book is available
		else if ($availability['bookID'] == $book)
		{
			$reserv_error = "<font size='5' color='red'>Warning: This book is not available! <br>Reserved on ".$availability['reservationDate'].".-It should be returned on ".$availability['restorationDate']."</font>";
			
		}
		if($restore < $reserve)
		{
			$reserv_error = "<font size='5' color='red'>Warning: 'Restore Date' should be greater than 'Reserve Day'!</font>";
		}
		else
		{
			$db->AddReservation($cust, $book, $reserve, $restore);
			$reserve_added = 1;
			//$db->AddOrder($_GET['selection_cust'], $_GET['selection_book']);
			
		}
	}
	
	//Restore Book
	if(!empty($_GET['restoreBook']) && !empty($_GET['_restorationDate']))
	{
		$bookid = $_GET['restoreBook'];
		$restorationDate = $_GET['_restorationDate'];
		
		$sql_update_book_status = "UPDATE books SET status='restored' WHERE id='$bookid'";
		//$sql_update_restoration = "UPDATE reservations SET restorationDate=CONCAT('$restorationDate', '.') WHERE bookID=8";
		
		mysqli_query($conn, $sql_update_book_status);
		//mysqli_query($conn, $sql_update_restoration) or die("could not update date");
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
     <!-- MORRIS CHART STYLES-->
   
        <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
     <!-- GOOGLE FONTS-->
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
     <!-- TABLE STYLES-->
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
	
		<!-- JQuery-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	
		<!-- JQUERY DATEPICKER -->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="/resources/demos/style.css">
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	
	<script>
		$( function() {
			$( "#fromdate" ).datepicker({
				dateFormat: "yy-mm-dd"
			});
			
			$( "#todate" ).datepicker({
				dateFormat: "yy-mm-dd"
			});
		} );
  </script>
	
</head>
<body>
    <div id="wrapper">
        <nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0">
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
font-size: 16px;"> <a href="logout.php?logout=1" class="btn btn-danger square-btn-adjust">Logout</a>
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
                        <a href="home.php"><i class="fa fa-desktop fa-3x"></i> Dashboard</a>
                    </li>
				   <li  >
                        <a   href="orders.php"><i class="glyphicon glyphicon-shopping-cart fa-3x"></i> Customers orders</a>
                    </li>	
                      <li >
                        <a class="active-menu" href="reservations.php"><i class="glyphicon glyphicon-calendar fa-3x"></i> Books Reservations</a>
                    </li>
                    <li  >
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
                <div class="row"style="position:relative;">
                    <div class="col-md-12">
                     <h2>Reservations</h2> 
                       
                    </div>
                </div>
                 <!-- /. ROW  -->
                 <hr />
              <?php echo $reserv_error; ?> 
            <div class="row">
                <div class="col-md-12">
                    <!-- Reservations-->               
                    <div class="panel panel-default">
                        <div class="panel-heading">
                           <h4><b>Books Reservations</b></h4>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive" style="max-height:500px; width:100%; overflow-y: auto;">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width:10%;">Info</th>
                                            <th>Customer</th>
                                            <th>Book Title</th>
											 <th>Date of Reservation</th>
											 <th>Date of Restoration</th>
											 <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<tr>
											<form action="#" method="GET">
												<td><b>Add Reservation</b></td>
													
														<td>
															<div class="form-group">
																<select class="form-control" name="selection_cust">
																	<?php

																		while (	$row_cust = mysqli_fetch_array($result_cust))
																		{
																			if(!empty($row_cust['id']))
																			{
																				echo "<option value= '".$row_cust['id']."'>"
																						.$row_cust['id']." "
																						.$row_cust['firstName']." "
																						.$row_cust['lastName'].
																					"</option>";
																			}
																		}
																	?>
																</select>
															</div>
														</td>
													<!--</form>-->
												<td>
													<!--<form action="#" method="GET">-->
													<div class="form-group">
					
														<select class="form-control" name="selection_book">
															<?php

																while (	$row_book = mysqli_fetch_array($result_book))
																{
																	if(!empty($row_book['id']))
																	{
																		echo "<option value= '".$row_book['id']."'>"
																				.$row_book['id']." "
																				.$row_book['title']." - "
																				.$row_book['authorName']." "
																				.$row_book['category'].
																			"</option>";
																	}
																}
															?>
														</select>
													
												</td>
												<td><input readonly required="required" id="fromdate" type="text" name="reserve" placeholder="format: YYYY-MM-DD"></td>
												<td><input readonly required="required" id="todate" type="text"name="restore" placeholder="format: YYYY-MM-DD"></td>
												<td><input type="submit" value="Add" style="background-color:#eeffe6;"></td>
											</form>
										</tr>
									</tbody>
									<tbody id="all-reservs">
                                        
                                    </tbody>
                                </table>
								
                            </div>
                        </div>
                    </div>
					
					<!-- ROW RESERVED & DELAYED-->
					<div class="panel panel-default">
							<div class="panel-heading" style="position:relative;">
								<h4><b>Reserved And Delayed Books</b></h4>
							</div>
					<div class="panel-body" style="margin-bottom: -2%;">
							
								<table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style='width:5%;'># id</th>
                                            <th style='width:25%;'>Title</th>
                                            <th style='width:22%;'>Author</th>
                                            <th style='width:21%;'>Category</th>
											<th style='width:19%'>Status</th>
											<th style='width:8%;'>Action</th>
                                        </tr>
                                    </thead>
                                </table>
					</div>
					
					
					<div class="panel-body" style="height:400px; margin-top: -2%; overflow-y: auto;">
							<div class="table-responsive" >
								<table class="table table-striped table-bordered table-hover">
									<tbody id="all-non-available">
											<!-- HERE ARE DISPLAYED THE EMPLOYEES (by ajax)-->
									</tbody>
								</table>
							</div>
						</div>
						</div>
					</div>
				</div>
                    
			 </div>
            </div>
			
			
                    <!--  end  Context Classes  -->
		</div> 
         <!-- /. PAGE WRAPPER  -->
     <!-- /. WRAPPER  -->
    <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <!-- JQUERY SCRIPTS 
    <script src="assets/js/jquery-1.10.2.js"></script>
      <!-- BOOTSTRAP SCRIPTS -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- METISMENU SCRIPTS 
    <script src="assets/js/jquery.metisMenu.js"></script>
     <!-- DATA TABLE SCRIPTS -->
    <script src="assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
	      <!-- CUSTOM SCRIPTS -->
    <script src="assets/js/custom.js"></script>
	
        <script>
            $(document).ready(function () {
                $('#dataTables-example').dataTable();
            });
		</script>

	<script>
		var reservation = "<?php echo $reserve_added; ?>";
	
		if(reservation == 1)
		{
			alert("Book Reserved Succesfully");
			window.location.replace("reservations.php");
		}
	
		function updateReservations() 
		{
			//alert('a');
			$.ajax({
				// url of script to be run
				url: "/library/php/lookForReservations.php/",
				
				// function to be run when script successfully sends data
				success: function( result ) {
					
					// get the json object of result (de-serialization)
					var obj = JSON.parse(result);
					
					//Clean the reservation list in order to display a refreshed one
					$("#all-reservs").html('');
					
					// add new reservation' content in the reservation list (#all-reservs table)
					$("#all-reservs").prepend(obj['reserv']);
				},
				
				// type of request -> POST
				type: "POST"
			});
		};	
		
		function updateBooks() 
		{
			//alert('b');
			$.ajax({
				// url of script to be run
				url: "/library/php/lookForBooks.php/",
				
				// function to be run when script successfully sends data
				success: function( result ) {
					
					// get the json object of result (de-serialization)
					var obj = JSON.parse(result);
					//alert(obj['books']);
					//Clean the reservation list in order to display a refreshed one
					$("#all-non-available").html('');
					
					// add new reservation' content in the reservation list (#all-reservs table)
					$("#all-non-available").prepend(obj['books']);
				},
				
				// type of request -> POST
				type: "POST"
			});
		};
		
		function restoreBook(id,restorationDate)
		{
			convertDate =function(a) {
			
				if(a == "Mon") return '01';		
				if(a == "Tue") return '02';
				if(a == "Wed") return '03';
				if(a == "Thu") return '04';
				if(a == "Fri") return '05';
				if(a == "Sat") return '06';
				if(a == "Sun") return '07';
				
				if(a == "Jan") return '01';
				if(a == "Feb") return '02';
				if(a == "Mar") return '03';
				if(a == "Apr") return '04';
				if(a == "May") return '05';
				if(a == "Jun") return '06';
				if(a == "Jul") return '07';
				if(a == "Aug") return '08';
				if(a == "Sept") return '09';
				if(a == "Oct") return '10';
				if(a == "Nov") return '11';
				if(a == "Dec") return '12';
			}
			
			var book_id = id;
			
			//Initialization of the date (js format)
			var restoration_date = Date(restorationDate);
			
			//Get the day of the date
			var _day = restoration_date.split(' ')[0];
			var day = convertDate(_day);
			
			//Get the month of the date
			var _month = restoration_date.split(' ')[1];
			var month = convertDate(_month);
			
			//Get the year of the date
			var year = restoration_date.split(' ')[3]
			
			//Concat the results (mysql format: yy-mm-dd)
			var date = year+"-"+month+"-"+day;
			
			$.ajax({
				
				url: '/library/reservations.php',
				data:{
					restoreBook : book_id,
					_restorationDate : date
				},
				type: 'GET',
				success: function(result){
					//alert(result);
				}
			})
		}
		
		$(document).ready(function (){
			// call the updateReservations() function every second
			// 500 => integer number in msec
			setInterval(updateReservations, 500);
			setInterval(updateBooks, 500);
		});
	</script>
   
</body>
</html>
