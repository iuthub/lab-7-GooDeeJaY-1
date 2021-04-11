<?php
session_start();

include('connection.php');

$status = '';

if (isset($_GET['logout'])){
    session_destroy();
    header("Location: index.php");
}

if (isset($_SESSION['user'])){
    $uid = $_SESSION['user']['id'];
    $get_posts->execute(array($uid));
    $posts = $get_posts->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['post'])) {
        $title = $_POST['title'];
        $body = $_POST['body'];
        $add_post->execute(array($title, $body, $uid));
        header("Location: index.php");

    }
    if (isset($_POST['login'])){
        $username = $_POST['username'];
        $pwd = $_POST['pwd'];
        $remember = isset($_POST['remember']);

        $login->execute(array($username, hash("md5", $pwd)));
        $data = $login->fetch();
        if ($data){
            $_SESSION['user'] = $data;
            if ($remember){
                $exp_date = time()+60*60*24*365;
                setcookie('username', $username, $exp_date);
                setcookie('pwd', $pwd, $exp_date);
            } else {
                unset($_COOKIE['username']);
                unset($_COOKIE['pwd']);
                setcookie('username', '', time()-1);
                setcookie('pwd', '', time()-1);
            }
            header("Location: index.php");
        } else {
            $status = "Incorrect login!";
        }
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<title>My Personal Page</title>
		<link href="style.css" type="text/css" rel="stylesheet" />
	</head>
	
	<body>
		<?php include('header.php'); ?>

        <?php if(!isset($_SESSION["user"])): ?>
		<!-- Show this part if user is not signed in yet -->
		<div class="twocols">
			<form action="index.php" method="post" class="twocols_col">
				<ul class="form">
					<li>
						<label for="username">Username</label>
						<input type="text" name="username" id="username" value="<?= $_COOKIE['username'] ?? '' ?>" required />
					</li>
					<li>
						<label for="pwd">Password</label>
						<input type="password" name="pwd" id="pwd" value="<?= $_COOKIE['pwd'] ?? '' ?>" required />
					</li>
					<li>
						<label for="remember">Remember Me</label>
						<input type="checkbox" name="remember" id="remember" />
					</li>
                    <div class="info-panel popout">
                        <?=$status?>
                    </div>
					<li>
						<input type="submit" name="login" value="Login" /> &nbsp; Not registered? <a href="register.php">Register</a>
					</li>
				</ul>
			</form>
			<div class="twocols_col">
				<h2>About Us</h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consectetur libero nostrum consequatur dolor. Nesciunt eos dolorem enim accusantium libero impedit ipsa perspiciatis vel dolore reiciendis ratione quam, non sequi sit! Lorem ipsum dolor sit amet, consectetur adipisicing elit. Optio nobis vero ullam quae. Repellendus dolores quis tenetur enim distinctio, optio vero, cupiditate commodi eligendi similique laboriosam maxime corporis quasi labore!</p>
			</div>
		</div>
        <?php else: ?>
		<!-- Show this part after user signed in successfully -->
		<div class="logout_panel"><a href="register.php">My Profile</a>&nbsp;|&nbsp;<a href="index.php?logout=1">Log Out</a></div>
        <br/>
		<h2>New Post</h2>
		<form action="index.php" method="post">
			<ul class="form">
				<li>
					<label for="title">Title</label>
					<input type="text" name="title" id="title" required/>
				</li>
				<li>
					<label for="body">Body</label>
					<textarea name="body" id="body" cols="30" rows="10" required></textarea>
				</li>
				<li>
					<input type="submit" name="post" value="Post" />
				</li>
			</ul>
		</form>
		<div class="onecol">
            <?php
            foreach ($posts as $post){
                $date = DateTime::createFromFormat('Y-m-d', $post['publishDate'])->format('M j, Y');
                $author = $_SESSION['user']['fullname'];
                $title = strtoupper($post['title']);

                echo "<div class='card'>";
                echo "<h2>$title</h2>";
                echo "<h5> $author, $date </h5>";
                foreach (explode("\n", $post['body']) as $p){
                    echo "<p> $p </p>";
                }
			    echo "</div>";
            }
            ?>
		</div>
        <?php endif; ?>
	</body>
</html>