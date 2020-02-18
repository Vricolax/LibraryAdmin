<?php
	if(!isset($_SESSION))
		session_start();
	
	include_once "php/database.php";
	include_once "php/partner.php";
	
	//Initialize objects
	$partner = new Partner();
	$db = new Database();
	
	//Boolean variables used to check whether the user added a partner or submitted an order
	$partner_added = 0;
	$order_added = 0;
	
	//Get all the partners from the Database
	$conn = mysqli_connect('localhost','root','','library');
	$sql = "SELECT * FROM partners ORDER BY id";
	$all_partners = mysqli_query($conn,$sql);
	
	//Error messages
	$partner_error = "";
	
	//Add new Partner to the Database
	if(!empty($_POST['partnerName']) && !empty($_POST['location']))
	{
		$name = $_POST['partnerName'];
		$location = $_POST['location'];
		
		$sql_p = "SELECT * FROM partners WHERE partnerName = '$name'";
		$validate_partner = mysqli_query($conn, $sql_p);
		$_partner = mysqli_fetch_array($validate_partner);
			
		//Check if the partner already exists in the database
		if($_partner['partnerName'] == $name)
		{
			$partner_error = "<font size='5' color='red'>Warning: This partner already exists!</font>";
			$name = "";
			$location = "";
		}
		else
		{
			$partner->_constructor($name,$location);
			$db->AddPartner($partner->GetName(), $partner->GetLocation());
			$partner_added = 1;
		}
	}
	
	//Add new Partner + Order to the Database
	if(!empty($_GET['selection']) && !empty($_GET['order']))
	{		
		$db->AddPartnerWithOrder($_GET['selection'], $_GET['order']);
		echo $_GET['selection']." ".$_GET['order'];
		$order_added = 1;
	}
	
	//Delete partner based on his id
	if(isset($_POST['_partnerID']))
	{
		$partID = $_POST['_partnerID'];
		$sql_del_part = "DELETE FROM partners WHERE id='$partID'";
		mysqli_query($conn,$sql_del_part) or die("Could not delete the partner from  the database");
		
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
	
	<!-- JQuery-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	
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
                        <a  href="home.php"><i class="fa fa-desktop fa-3x"></i> Dashboard</a>
                    </li>
						   <li  >
                        <a   href="orders.php"><i class="glyphicon glyphicon-shopping-cart fa-3x"></i> Customers orders</a>
                    </li>	
                      <li  >
                        <a  href="reservations.php"><i class="glyphicon glyphicon-calendar fa-3x"></i> Books Reservations</a>
                    </li>
                    <li  >
                        <a  href="management.php"><i class="fa fa-edit fa-3x"></i> Control Panel</a>
                    </li>
                  <li>
                        <a class="active-menu"  href="partners.php"><i class="fa fa-sitemap fa-3x"></i> Partners</a>
                    </li>	
                </ul>
               
            </div>
            
        </nav>  
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <h2><b>Partners</b></h2>   
                        <h5><b>Loan books from partners</b></h5>
                       
                    </div>
				
				
                </div>
                 <!-- /. ROW  -->
                 <hr />
				<div class="panel panel-default" style="position:relative;">
						<div class="panel-heading">
							<h4><b>Add new Partner or Order</b></h4>
						</div>
				<div class="panel-body">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>Info</th>
								<th>Partner Name</th>
								<th>Location</th>
								 <th>Action</th>
							</tr>
						</thead>
						<tbody>
							<tr>
							<?php echo $partner_error; ?>
								<form action="partners.php" method="POST">
									<td><b>Add Partner</b></td>
									<td><input name="partnerName" pattern="[a-zA-Z]*" required></td>
									<td><input name="location" pattern="[a-zA-Z]*" required></td>
									<td><input type="submit" value="Add" style="background-color:#eeffe6;"></td>
								</form>
							</tr>                                      
						</tbody>
					</table>
					
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>Info</th>
								<th style='width:60%;'>Select Partner</th>
								 <th>Order</th>
								 <th>Action</th>
							</tr>
						</thead>
						<tbody>
							<tr>
									<td><b>Add Order</b></td>
									<td>
										<div class="form-group">
										<form action="#" method="GET">
											<select class="form-control" name="selection">
												<?php 
													
													while (	$row = mysqli_fetch_array($all_partners))
													{
														if(!empty($row['id']))
														{
															echo "<option value= '".$row['id']."'>"
																	.$row['id'].") "
																	.$row['partnerName']." || Location: "
																	.$row['location'].
																"</option>";
														}
													}
												?>
											</select>												
										</div>
									</td>
									<td><textarea name="order" id="id_order" rows="3" cols="50" required>Write your order here...</textarea></td>
									<td><input type="submit" value="Submit" onclick="javascript:validateOrder();" style="background-color:#eeffe6;"></td>
								</form>
							</tr>                                      
						</tbody>
					</table>
					</div>
				</div>
				
				<!-- Our Partner -->
				<div class="panel panel-default" style="position:relative;">
					<div class="panel-heading">
						<h4><b>Our Partners</b></h4>
					</div>
					<div class="panel-body">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>Partner Name</th>
								<th>Location</th>
								<?php if($_SESSION['username'] == "admin")
									echo "<th>Action</th>";
								?>
							</tr>
						</thead>
						<tbody id='all-partners'>
							<!-- Here are displayed all the partners-->                                
						</tbody>
					</table>
					</div>
				</div>
				
				<!-- Orders View -->
				<div class="panel panel-default" style="position:relative;">
							<div class="panel-heading">
								<h4><b>Our Orders to the Partners</b></h4>
							</div>
					<div class="panel-body">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th style='width:40%;'>Partner</th>
								<th>Order</th>
							</tr>
						</thead>
						<tbody id="all-orders">
							<!-- All the orders made to the partners are shown here -->					
						</tbody>
					</table>
					</div>
				</div>
				
    </div>
				<!-- Amazon Stuff-->
				<div style="margin-left:2%;">	
					<form method="get" target="_blank" action="https://www.amazon.com/s/">
						<input type="hidden" name="url" value="search-alias=aps" />
						<h2>Order from Amazon</h2><hr>
						<img src="images/amazon_logo.png" width="30" height="30"><img>
						Search Amazon: <input type="text" name="field-keywords" />
						<input type="submit" name="Go" value="Go" />
					</form><br><br>
				</div>
				<div style="margin-left:2%;">
					<h3>Recommendations by <a target="_blank" href="https://www.amazon.co.uk/books-used-books-textbooks/b/ref=nav_shopall_bo_books?ie=UTF8&node=266239"><img src="images/amazon_logo2.png" width="200" height="100" ><img></a>for this month</h3>
				</div>
				
				<div style="margin-left:2%;">
					<iframe type="text/html" width="336" height="550" frameborder="0" allowfullscreen style="max-width:100%; margin-right:3%;" src="https://read.amazon.com/kp/card?asin=B00NOPQU2K&preview=inline&linkCode=kpe&ref_=cm_sw_r_kb_dp_zY6bzbYGPJFZF" ></iframe>
					<iframe type="text/html" width="336" height="550" frameborder="0" allowfullscreen style="max-width:100%; margin-right:3%;" src="https://read.amazon.com/kp/card?asin=B01M0R1Y1J&preview=inline&linkCode=kpe&ref_=cm_sw_r_kb_dp_82pazb8GCBY69" ></iframe>
					<iframe type="text/html" width="336" height="550" frameborder="0" allowfullscreen style="max-width:100%; margin-right:3%;" src="https://read.amazon.com/kp/card?asin=B01C652QGC&preview=inline&linkCode=kpe&ref_=cm_sw_r_kb_dp_17pazbYZTYD1G" ></iframe>
					<iframe type="text/html" width="336" height="550" frameborder="0" allowfullscreen style="max-width:100%; margin-right:3%;" src="https://read.amazon.com/kp/card?asin=B01H3MWEME&preview=inline&linkCode=kpe&ref_=cm_sw_r_kb_dp_i9pazbGN3GNY4" ></iframe>
				</div>
				<!-- /Amazon Stuff-->
				
             <!-- /. PAGE INNER  -->
            </div>
			
			
         <!-- /. PAGE WRAPPER  -->
        </div>
     <!-- /. WRAPPER  -->
    <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <!-- JQUERY SCRIPTS 
    <script src="assets/js/jquery-1.10.2.js"></script>-->
      <!-- BOOTSTRAP SCRIPTS -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="assets/js/jquery.metisMenu.js"></script>
      <!-- CUSTOM SCRIPTS -->
    <script src="assets/js/custom.js"></script>
    
	<script>
		var order = document.getElementById("id_order"),
			partnerAdded = "<?php echo $partner_added; ?>",
			orderAdded = "<?php echo $partner_added; ?>";
		
		var user = "<?php echo $_SESSION['username']; ?>";
		
		//Message informs the user that the partner has been added
		if(partnerAdded == 1)
		{
			alert("Partner added succesfully");
			//Page refresh in order to show the newer partners added in the tables
			window.location.replace("partners.php");
		}
		//Message informs the user that the order has been submitted
		if(orderAdded == 1)
		{
			alert("Order added succesfully");
		}
		
		function validateOrder()
		{
			if (order.value == "Write your order here..." )
			{
				order.setCustomValidity("Please Write Your Order");
			}else
				order.setCustomValidity('');
		}
		
		order.onchange = validateOrder;
		order.onkeyup = validateOrder;
		
		//Update Partner Orders
		function updateOrders()
		{
			$.ajax({
				url: "/library/php/lookForPartOrders.php",
				success: function(result){
					var obj = JSON.parse(result);
					//alert(obj['partOrders']);
					$("#all-orders").empty().prepend(obj['partOrders']);
				}
			});
		}
		
		//Update all the partners from the Database
		function updatePartners()
		{
			$.ajax({
				url: "/library/php/lookForPartners.php",
				success: function(result){
					var obj = JSON.parse(result);
					//alert(obj['partner']);
					$("#all-partners").empty().prepend(obj['partner']);
				}
			});
		}
		
		//Delete partner when "delete" button is pressed
		function deletePartner(id)
		{
			var partner_id = id;
			
			if(user == "admin")
			{				
				$.ajax({
					type: "POST",
					url: "/library/partners.php",
					data: { 
						_partnerID: partner_id
					},
					success: function(result){	
						//alert('"working");
					}
					
				});
			}
			else
				alert("You do not have the permission to perform this action! For More information, please contact the Administrator.");				
		}
		
		$(document).ready(function()
		{
			setInterval(updateOrders,500);
			setInterval(updatePartners,500);
		});
		
		
		
	</script>


</body>
</html>
