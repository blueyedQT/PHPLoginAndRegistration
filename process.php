<?php
session_start();
require_once('connection.php');

function logout()
{
	$_SESSION = array();
	session_destroy();
}

function register($connection, $post)
{
	foreach ($post as $name => $value) 
	{
		if(empty($value))
		{
			$_SESSION['error'][$name] = "sorry, " . $name . " cannot be blank.";
		}
		else {
			switch ($name) {
				case 'first_name':
				case 'last_name':
					if(is_numeric($value))
					{
						$_SESSION['error'][$name] = $name . ' cannot contain numbers!';
					}
				break;
				case 'email':
					if(!filter_var($value, FILTER_VALIDATE_EMAIL)) 
					{
						$_SESSION['error'][$name] = $name . " is not a valid email!";
					}
				break;
				case 'password':
					$password = $value;
					if(strlen($value) < 5) 
					{
						$_SESSION['error'][$name] = $name . ' must be greater than 5 characters.';
					}
				break;
				case 'confirm_password':
					if($password != $value) 
					{
						$_SESSION['error'][$name] = $name . 'Passwords do not match';
					}
				break;
				case 'birthdate':
					$birthdate = explode('/', $value);
					if(!checkdate($birthdate[0], $birthdate[1], $birthdate[2])) 
					{
						$_SESSION['error'][$name] = $name . ' is not a valid date!';
					}
				break;
			}
		}
	}

	// if($_FILES['file']['error'] > 0) 
	// {
	// 	$_SESSION['error']['file'] = "Error on file upload Return Code: " . $_FILES['file']['error'];
	// }
	// else 
	// {
	// 	$directory = 'uploads/';
	// 	$file_name = $_FILES['file']['name'];
	// 	$file_path = $directory . $file_name;
	// 	if(file_exists($_file_path))
	// 	{
	// 		$_SESSION['error']['file'] = $file_name . ' already exists';
	// 	}
	// 	else
	// 	{
	// 		if(!move_uploaded_file($_FILES['file']['temp_name'], $file_path))
	// 		{
	// 			$_SESSION['error']['file'] = $file_name . " could not be saved";
	// 		}
	// 	}
	// }

	if(!isset($_SESSION['error']))
	{
		$_SESSION['sucess_message'] = "Congratulations you are now a member!";

		$salt = bin2hex(openssl_random_pseudo_bytes(22));
		$hash = crypt($post['password'], $salt);

		$f_birthdate = $birthdate[2].'-'.$birthdate[0].'-'.$birthdate[1];
		$query = "INSERT INTO users (first_name, last_name, email, password, birthdate, file_path, created_at, updated_at) 
				  VALUES ('".$post['first_name']."', '".$post['last_name']."', '".$post['email']."', '".$hash."', '".$f_birthdate."', 'file_path_variable', NOW(), NOW())";
		mysqli_query($connection, $query);
		
		$user_id = mysqli_insert_id($connection);
		$_SESSION['user_id'] = $user_id;

		header('location: profile.php?id='.$user_id);
		exit;
	}
}
function login($connection, $post)
{
	if(empty($post['email']) || empty($post['password']))
	{
		$_SESSION['error']['message'] = "Email or password cannot be blank!";
	}
	else
	{
		$query = "SELECT id, password FROM users WHERE email = '".$post['email']."'";
		$result = mysqli_query($connection, $query);
		$row = mysqli_fetch_assoc($result);

		if(empty($row))
		{
			$_SESSION['error']['message'] = 'Could not find email in database';
		}
		else
		{
			if(crypt($post['password'], $row['password']) != $row['password'])
			{
				$_SESSON['error']['message'] = 'Incorrect password';
			}
			else {
				$_SESSION['user_id'] = $row['id'];
				header('location: profile.php?id='.$row['id']);
				exit;

			}
		}
	}
	header('location: login.php');
	exit;
}


if(isset($_POST['action']) && $_POST['action'] == 'register')
{
	register($connection, $_POST);
}
elseif (isset($_POST['action']) && $_POST['action'] == 'login')
{
	login($connection, $_POST);
}
elseif (isset($_GET['logout']))
{
	logout();
}

header('location: index.php');

?>