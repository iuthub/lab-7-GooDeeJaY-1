<?php
session_start();

$username = $f_name = $email = $pwd = $c_pwd = '';
$status = '';
$is_update = False;

include('connection.php');

if (isset($_SESSION['user'])){
    $username = $_SESSION['user']['username'];
    $f_name   = $_SESSION['user']['fullname'];
    $email    = $_SESSION['user']['email'];
    $is_update = True;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    foreach (array('username', 'f_name', 'email', 'pwd', 'c_pwd') as $var) {
        ${$var} = $_POST[$var];
    }

    if ($pwd == $c_pwd){
        $hash_pwd = hash("md5", $pwd);
        if ($is_update){
            $response = $update->execute(array($username, $email, $f_name, $hash_pwd, $_SESSION['user']['id']));
            if ($response){
                $status = "Data successfully changed, Login again!";
                session_destroy();
                unset($_COOKIE['username']);
                unset($_COOKIE['pwd']);
                setcookie('username', '', time()-1);
                setcookie('pwd', '', time()-1);
                header("Refresh: 1.5; URL=index.php");
            }
        } else {
            $exists->execute(array($username, $email));
            if ($exists->fetchColumn()){
                $status = "User already exists!";
            } else {
                if ($reg->execute(array($username, $email, $hash_pwd, $f_name))){
                    $status = "Successfully registered!";
                    header("Refresh: 1.5; URL=index.php");
                }
            }
        }
    } else {
        $status = "Passwords didn't match!";
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<title>My Blog - Registration Form</title>
		<link href="style.css" type="text/css" rel="stylesheet" />
	</head>
	
	<body>
		<?php include('header.php'); ?>

		<h2>User Details Form</h2>
		<h4>Please, fill below fields correctly</h4>
		<form action="register.php" method="post">
				<ul class="form">
					<li>
						<label for="username">Username</label>
						<input type="text" name="username" value="<?=$username?>" id="username" required/>
					</li>
					<li>
						<label for="f_name">Full Name</label>
						<input type="text" name="f_name" value="<?=$f_name?>" id="f_name" required/>
					</li>
					<li>
						<label for="email">Email</label>
						<input type="email" name="email" value="<?=$email?>" id="email" />
					</li>
					<li>
						<label for="pwd">Password</label>
						<input type="password" name="pwd" value="<?=$pwd?>" id="pwd" required/>
					</li>
					<li>
						<label for="c_pwd">Confirm Password</label>
						<input type="password" name="c_pwd" value="<?=$c_pwd?>" id="c_pwd" required />
					</li>
                    <div class="info-panel popout">
                        <?=$status?>
                    </div>
					<li>
						<input type="submit" value="Submit" /> &nbsp; Already registered? <a href="index.php">Login</a>
					</li>
				</ul>
		</form>
	</body>
</html>