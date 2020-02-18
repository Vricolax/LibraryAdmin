<?php
	if(!isset($_SESSION))
		session_start();
		
	include_once "php/database.php";
	include_once "php/book.php";
	include_once "php/customer.php";
	
	//Initialize objects
	$book = new Book();
	$db = new Database();
	$cust = new Customer();
	
	//Initialize ERROR variables
	$customer_error = "";
	$book_error = "";
	$category_error = "";
	

	
	//Get latest customer user number found in the database
	$conn = mysqli_connect('localhost','root','','library');

	
	//Get all the customers from the DB
	$sql2 = "SELECT * FROM customers ORDER BY id";
	$result = mysqli_query($conn, $sql2) or die("No customer found management.php result");
	$result4 = mysqli_query($conn, $sql2) or die("No customer found management.php result4");

	//Get all the books from the DB
	$sql_book = "SELECT * FROM books ORDER BY id";
	$result2 = mysqli_query($conn, $sql_book) or die("No book found management.php result2");
	$result3 = mysqli_query($conn, $sql_book) or die("No book found management.php result3");
	
	//Get all the book categories from the DB
	$sql_cat = "SELECT DISTINCT categoryName FROM categories";
	$result_categories = mysqli_query($conn, $sql_cat) or die ("No categories found management.php cat");
	$result_categories2 = mysqli_query($conn, $sql_cat) or die ("No categories found management.php cat");
	
	//Validate Added/Editted/Deleted Elements
	$book_added = 0;
	$customer_added =0;
	$category_added =0;
	$customer_edited =0;
	$book_edited =0;
	$book_deleted =0;
	$customer_deleted =0;
	
	/*//Error Messages
	$book_deleted_error =0;
	$customer_deleted_error =0;*/
	
	//Add new Book to the DB
	if(!empty($_GET['title']) && !empty($_GET['author']) && !empty($_GET['selectCategory']))
	{
		$title = $_GET['title'];
		$author = $_GET['author'];
		$category = $_GET['selectCategory'];
		
		$sql_b = "SELECT * FROM books WHERE title = '$title' AND authorName = '$author'";
		$validate_book = mysqli_query($conn, $sql_b);
		$_book = mysqli_fetch_array($validate_book);
		
		//Check if the book already exist
		if($_book['title'] == $title && $_book['authorName'] == $author)
		{
			$book_error = "<font size='5' color='red'>Warning: This book already exists!</font>";
			$title ="";
			$author="";
			$category="";
			$book_added =0;
		}
		else
		{
			echo $category;
			$book->_constructor($title, $author, $category);
			$db->AddBook($book->GetTitle(), $book->GetAuthor(), $book->GetCategory());
			$book_added =1;
			$book_error = "";
			//header("location: management.php");
		}
	}
	
	//Add new Category
	if(!empty($_GET['newCategory']))
	{
		$newcat = $_GET['newCategory'];
		$sql_c = "SELECT * FROM categories WHERE categoryName = '$newcat'";
		$validate_category= mysqli_query($conn, $sql_c);
		$_category = mysqli_fetch_array($validate_category);
		
		//Check if category already exists in the DB
		if($_category['categoryName'] == $_GET['newCategory'])
		{
			$category_error = "<font size='5' color='red'>Warning: This category already exists!</font>";
			$category_added = 0;
		}
		else
		{
			$category_added = 1;
			$category_error = "";
			$db->AddBookCategory($newcat);
		}
	}
	
	//Add new Customer to the DB
	if(!empty($_GET['fname']) && !empty($_GET['lname']) )
	{
		$fname = $_GET['fname'];
		$lname = $_GET['lname'];

		$sql = "SELECT * FROM customers WHERE firstName = '$fname' AND lastName = '$lname'";
		$validate_customer = mysqli_query($conn, $sql) or die("No customer found management.php");
		$_cust = mysqli_fetch_array($validate_customer);
		
		//Check if the customer already exist
		if($_cust['firstName'] == $_GET['fname'] && $_cust['lastName'] == $_GET['lname'])
		{
			$customer_error = "<font size='5' color='red'>Warning: This customer already exists!</font>";
			$fname = "";
			$lname = "";
			$customer_added = 0;
		}
		else
		{
			$cust->_constructor($fname, $lname);
			$db->AddCustomer($cust->GetFirstName(), $cust->GetLastName());
			$customer_added = 1;
			$customer_error = "";
			//header("location: management.php");
		}
	}

	//Update/Edit Customers
	if(!empty($_GET['editFname']) && !empty($_GET['editLname']) && !empty($_GET['selection']))
	{
		$db->EditCustomer($_GET['selection'], $_GET['editFname'], $_GET{'editLname'});
		$customer_edited = 1;
		//header("location: management.php");
	}
	
	//Get selected customer
	$cust_fname = "";
	/*if(!empty($_GET['selection']))
	{
		$cust_fname = $_GET['selection'];
	}*/
	
	//Update/Edit Books
	if(!empty($_GET['editAuthor']) && !empty($_GET['editBook']) && !empty($_GET['editCategory']) && !empty($_GET['selection2']))
	{
		$db->EditBook($_GET['selection2'], $_GET['editAuthor'], $_GET['editBook'], $_GET{'editCategory'});
		$book_edited = 1;
		//header("location: management.php");
	}
	
	//Delete Books
	if(!empty($_GET['selection3']))
	{
		//if($_SESSION['username'] == "admin")
		//{
			$db->DeleteBook($_GET['selection3']);
			$book_deleted = 1;
		//}
		//else
			$book_deleted_error = 1;
	}
	
	//Delete Customers
	if(!empty($_GET['selection4']))
	{
		//if($_SESSION['username'] == "admin")
		//{
			$db->DeleteCustomer($_GET['selection4']);
			$customer_deleted = 1;
		//}
		//else
			$customer_deleted_error = 1;
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
                      <li  >
                        <a  href="reservations.php"><i class="glyphicon glyphicon-calendar fa-3x"></i> Books Reservations</a>
                    </li>
                    <li  >
                        <a class="active-menu" href="management.php"><i class="fa fa-edit fa-3x"></i> Control Panel</a>
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
                     <h2><b>Edit Customers</b></h2><br>                        
                    </div>
                </div>
                 <!-- /. ROW  -->
				 
				 <div class="row" >
                    <div class="col-md-9 col-sm-12 col-xs-12">
               
                    <div class="panel panel-default">
                        <div class="panel-heading">
                           <h4><b>Edit Customer</b></h4>
                        </div>
						<!-- ADD CUSTOMERS-->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
											<th>Info</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
											 <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<tr>
											<form action="management.php" method="GET">
												<td><b>Add Customer</b></td>
												<td><input name="fname" required="required" pattern="[a-zA-Z]*"></td>
												<td><input name="lname" required="required" pattern="[a-zA-Z]*"></td>
												<td><input type="submit" value="Add" style="background-color:#eeffe6;"></td>
											</form>
										</tr>                                      
                                    </tbody>
                                </table>
								<?php echo $customer_error; ?>
                            </div>
							<!-- /ADD CUSTOMERS-->
							<!-- EDIT CUSTOMERS-->
							<div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
											<th>Info</th>
                                            <th>Select ID</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
											 <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<tr>
												<td><b>Edit Customer</b></td>
												<td>
													<div class="form-group">
													<form action="#" method="GET">
														<select class="form-control" name="selection" id="customerr">
															<?php 
																
																while (	$row = mysqli_fetch_array($result))
																{
																	if(!empty($row['id']))
																	{
																		echo "<option value= '".$row['id']."'>"
																				.$row['id']." "
																				.$row['firstName']." "
																				.$row['lastName'].
																			"</option>";
																	}
																}
															?>
														</select>												
													</div>
												</td>
												<td><input name="editFname" id="cust_fname" required="required" pattern="[a-zA-Z]*"></td>
												<td><input name="editLname" id="cust_lname" required="required" pattern="[a-zA-Z]*"></td>
												<td><input type="submit" value="Edit" style="background-color:#eeffe6;"></td>
											</form>
										</tr>                                      
                                    </tbody>
                                </table>
                            </div>
							<!-- /EDIT CUSTOMERS-->
							
							<!-- DELETE CUSTOMERS-->
							<div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
											<th>Info</th>
                                            <th style="width:80%;">Select Customer</th>
											 <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<tr>
											<form action="#" method="GET">
												<td><b>Delete Customer</b></td>
												<td>
													<div class="form-group">
												
														<select class="form-control" name="selection4">
															<?php

																while (	$row_cust = mysqli_fetch_array($result4))
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
												<td><input type="submit" value="Delete" style="background-color:#ffcccc;"></td>
											</form>
										</tr>                                      
                                    </tbody>
                                </table>
                            </div>
                        </div>
                     </div>
					 
					  
					 <!-- ROW BOOKS -->
					 <div class="row">
                    <div class="col-md-12">
                     <h2><b>Edit Books</b></h2><br>                        
                    </div>
                </div>
					 
					 <div class="panel panel-default">
                        <div class="panel-heading">
                           <h4><b>Edit Book</b></h4>
                        </div>
						<!-- ADD BOOKS-->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
											<th>Info</th>
                                            <th>Author's Name</th>
                                            <th>Book Title</th>
                                             <th>Category</th>
											 <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<tr>
											<form action="management.php" method="GET">
												<td><b>Add Book</b></td>
												<td><input name="title" required="required" pattern="[a-zA-Z ]*"></td>
												<td><input name="author" required="required" pattern="[a-zA-Z ]*"></td>
												<td>
													<select class="form-control" name="selectCategory">
														<?php
															while (	$row_category = mysqli_fetch_array($result_categories))
															{
																if(!empty($row_category['categoryName']))
																{
																	echo "<option value= '".$row_category['categoryName']."'>"
																			.$row_category['categoryName'].
																		"</option>";
																}
															}	
														?>
													</select>
												</td>
												<td><input type="submit" value="Add" style="background-color:#eeffe6;"></td>
											</form>
										</tr>                                      
                                    </tbody>
                                </table>
                            </div>
							<?php echo $book_error; ?>
							<!-- /ADD BOOKS-->
							<!-- EDIT BOOKS-->
							<div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
											<th>Info</th>
                                            <th>Select ID</th>
                                            <th>Author's Name</th>
                                            <th>Book Title</th>
                                             <th>Category</th>
											 <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<tr>
											<td><b>Edit Book</b></td>
											<td>
												<div class="form-group">
												<form action="#" method="GET">
													<select class="form-control" name="selection2" id="bookk">
														<?php
															while (	$row_book = mysqli_fetch_array($result2))
															{
																if(!empty($row_book['id']))
																{
																	echo "<option value= '".$row_book['id']."'>"
																			.$row_book['id']." - "
																			.$row_book['title']." - "
																			.$row_book['authorName'].
																		"</option>";
																}
															}	
														?>
													</select>
												</div>
											</td>
											<td><input id="book_author" name="editAuthor" required="required" pattern="[a-zA-Z ]*"></td>
											<td><input id="book_title" name="editBook" required="required"></td>
											<td>
												<select class="form-control" name="editCategory">
													<?php
														while (	$row_category = mysqli_fetch_array($result_categories2))
														{
															if(!empty($row_category['categoryName']))
															{
																echo "<option value= '".$row_category['categoryName']."'>"
																		.$row_category['categoryName'].
																	"</option>";
															}
														}	
													?>
												</select>
											</td>
											<td><input type="submit" value="Edit" style="background-color:#eeffe6;"></td>
										</form>
										</tr>                                      
                                    </tbody>
                                </table>
                            </div>
							<!-- /EDIT BOOKS-->
							
							<!-- DELETE BOOKS-->
							<div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
											<th>Info</th>
                                            <th style="width:80%;">Select Book</th>
											 <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<tr>
											<form action="#" method="GET">
												<td><b>Delete Book</b></td>
												<td>
													<div class="form-group">
												
														<select class="form-control" name="selection3">
															<?php

																while (	$row_book = mysqli_fetch_array($result3))
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
													</div>
												</td>
												<td><input type="submit" value="Delete" style="background-color:#ffcccc;"></td>
											</form>
										</tr>                                      
                                    </tbody>
                                </table>
                            </div>
							<!-- /DELETE BOOKS-->
							<?php echo $category_error; ?>
							<!-- ADD BOOK CATEGORY-->
							<div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
											<th>Info</th>
											<th>Category Title</th>
											<th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<tr>
											<form action="management.php" method="GET">
												<td><b>Add Book</b></td>
												<td><input name="newCategory" required="required" pattern="[a-zA-Z ]*"></td>
												<td><input type="submit" value="Add" style="background-color:#eeffe6;"></td>
											</form>
										</tr>                                      
                                    </tbody>
                                </table>
                            </div>
                        </div>
                     </div>
					 
					 
					 
					 
                    
                    </div>
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
      <!-- CUSTOM SCRIPTS -->
    <script src="assets/js/custom.js"></script>
	
	<script>
		var bookAdded = "<?php echo $book_added; ?>",
			customerAdded = "<?php echo $customer_added; ?>",
			categoryAdded = "<?php echo $category_added; ?>";
			
			
		var	bookDeleted= "<?php echo $book_deleted; ?>",
			customerDeleted= "<?php echo $customer_deleted; ?>";
		
		var	customerEdited = "<?php echo $customer_edited; ?>";
		var bookEdited= "<?php echo $book_edited; ?>";
		
		
		//Update Edit Customer input fields (value) based on combobox selection
		var strUser = "";
		var cust = document.getElementById("customerr");
		cust.onchange = (function()
		{
			strUser = cust.options[cust.selectedIndex].text;
			var splitStr = strUser.split(/(\s+)/);
			$("#cust_fname").val(splitStr[2]);
			$("#cust_lname").val(splitStr[4]);
		
		});
		
		//Update Edit Book input fields (value) based on combobox selection
		barstrBook = "";
		var book = document.getElementById("bookk");
		book.onchange = (function()
		{
			strUser = book.options[book.selectedIndex].text;
			var splitStr = strUser.split('-');
			$("#book_author").val(splitStr[2]);
			$("#book_title").val(splitStr[1]);
		
		});
		
		//Success messages
		if (bookAdded == 1)
		{
			alert("Book added succesfully");
			window.location.replace("management.php");
		}
		if(customerAdded == 1)
		{
			alert("Customer added succesfully");
			window.location.replace("management.php");
		}
		if(categoryAdded == 1)
		{
			alert("Category added succesfully");
			window.location.replace("management.php");
		}
		if(customerEdited == 1)
		{
			alert("Customer Edited succesfully");
			window.location.replace("management.php");
		}
		if(bookEdited == 1)
		{
			alert("Book Edited succesfully");
			window.location.replace("management.php");
		}
		if(bookDeleted == 1)
		{
			alert("Book Deleted succesfully");
			window.location.replace("management.php");
		}
		if(customerDeleted == 1)
		{
			alert("Customer Deleted succesfully");
			window.location.replace("management.php");
		}
		/*
		//Error messages
		if(customerDeletedError == 1)
		{
			alert("You do not have the permission to perform this action! For More information, please contact the Administrator.");
			window.location.replace("management.php");

		}
		if(bookDeletedError == 1)
		{
			alert("You do not have the permission to perform this action! For More information, please contact the Administrator.");
			window.location.replace("management.php");

		}*/
	</script>
    
   
</body>
</html>
