<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Registration</title>
</head>
<body>
	<h1>Welcome - Registration</h1>
<?php 
	if(isset($_SESSION['error'])) {
		foreach($_SESSION['error'] as $name => $message) {
			?>
			<p><?=$message ?></p>
			<?php
		}
	} elseif(isset($_SESSION['sucess_message'])) { ?>
			<p><?=$_SESSION['sucess_message'] ?></p>
<?php
	} ?>
	<form action="process.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="register">
		<input type="text" name="first_name" placeholder="Enter First Name">
		<input type="text" name="last_name" placeholder="Enter Last Name">
		<input type="text" name="email" placeholder="Enter Email">
		<input type="password" name="password" placeholder="Password">
		<input type="password" name="confirm_password" placeholder="Confirm Password">
		<input type="text" name="birthdate" placeholder="Enter Birthday MM/DD/YYYY">
		<input type="file" name="file">
		<input type="submit" value="Register">
	</form>
</body>
</html>
<?php $_SESSION = array(); ?>