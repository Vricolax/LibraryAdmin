<?php
	if(!isset($_SESSION))
		session_start();
	//Connect to the Database
	$conn = mysqli_connect("localhost","root","","library");
	
	//Retrieve all the orders from the DB
	$sql = "SELECT * FROM orders";
	$result = mysqli_query($conn,$sql);
	
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
    <link href="assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
     <!-- GOOGLE FONTS-->
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
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
font-size: 16px;"><a href="logout.php?logout=1" class="btn btn-danger square-btn-adjust">Logout</a>
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
                        <a class="active-menu" href="orders.php"><i class="glyphicon glyphicon-shopping-cart fa-3x"></i> Customers orders</a>
                    </li>	
                      <li  >
                        <a  href="reservations.php"><i class="glyphicon glyphicon-calendar fa-3x"></i> Books Reservations</a>
                    </li>
                    <li  >
                        <a  href="management.php"><i class="fa fa-edit fa-3x"></i> Control Panel</a>
                    </li>				
					
                  <li  >
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
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4><b>Customers Orders</b></h4>
							</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover" id="dataTables-customers">
										
										<thead>
											<tr>
												<th>FirstName</th>
												<th>Last Name</th>
												<th>Books Amount</th>
											</tr>
										</thead>
										<tbody>
											<?php
												
												while ($row = mysqli_fetch_array($result))
												{
													//Get the name of the customer based on his ID
													$cust_id = $row['custID'];
													$sql_cust = "SELECT * FROM customers WHERE id='$cust_id'";
													$result_cust = mysqli_query($conn, $sql_cust);
													$row_cust = mysqli_fetch_array($result_cust);
												
													
													echo "
													<tr >
														<td>".$row_cust['firstName']."</td>
														<td>".$row_cust['lastName']."</td>
														<td>".$row['booksAmount']."</td>
													</tr>";
												}
											?>
										</tbody>
									</table>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>
             <!-- /. PAGE INNER  -->
		</div>
         <!-- /. PAGE WRAPPER  -->
        </div>
     <!-- /. WRAPPER  -->
    <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <!-- JQUERY SCRIPTS -->
    <script src="assets/js/jquery-1.10.2.js"></script>
      <!-- BOOTSTRAP SCRIPTS -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="assets/js/jquery.metisMenu.js"></script>
	
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
    
   
</body>
</html>
