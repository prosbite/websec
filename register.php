<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form data and sanitize inputs
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if (!$username || !$fullname || !$email) {
        echo "Invalid input data.";
        exit;
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Database connection and insert using PDO
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=websec", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insert data using prepared statement
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $fullname, $email]);

        header("Location: index.php"); // Redirect to login page
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
	<style>
		.container {
			display:flex;
			width:100%;
			min-height:400px;
			align-items:center;
			padding-top:200px;
			flex-direction:column;
		}
		.form-element-container {
			display:flex;
			flex-direction:column;
		}
		.errmsg {
			color: red;
		}
		.mb-12 {
			margin-bottom:12px;
		}
		.mb-5 {
			margin-bottom:5px;
		}
		.mr-24 {
			margin-right:24px;
		}
		.btn-container {
			display:flex;
			justify-content: space-between;
		}
	</style>
</head>
<body>
    <form method="POST">
		<section class="container">
			<h1>REGISTER</h1>
			<div class="form-element-container mb-12">
				<label for="username">Username:</label>
				<input type="text" name="username" required>
			</div>

			<div class="form-element-container mb-12">
				<label for="password">Password:</label>
				<input type="password" name="password" required>
			</div>

			<div class="form-element-container mb-12">
				<label for="confirm_password">Confirm Password:</label>
				<input type="password" name="confirm_password" required>
			</div>

			<div class="form-element-container mb-12">
				<label for="fullname">Full Name:</label>
				<input type="text" name="fullname" required>
			</div>
			<div class="form-element-container mb-12">
				<label for="email">Email:</label>
				<input type="email" name="email" required>
			</div>
			<div class="btn-container">
				<a href="/index.php" class="mr-24">Login</a>
				<input type="submit" value="Register">
			</div>
		</section>
       
    </form>
</body>
</html>
