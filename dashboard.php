<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Dashboard</title>

	<style>
		.container {
			display:flex;
			width:100%;
			min-height:400px;
			align-items:center;
			padding-top:200px;
			flex-direction:column;
		}

		.fullname {
			color:#2980b9;
		}
		.container h1 {
			font-size:45px;
		}
	</style>
</head>
<body>
	<section class="container">
		<h1>Hi <span class="fullname"><?php echo $_SESSION['user']['full_name'] ?></span>, Welcome to the Dashboard</h1>
		<a href="/logout.php">Log Out</a>
	</section>
</body>
</html>