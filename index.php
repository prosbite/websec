<?php
session_start();

class User
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Function to sanitize input data
    private function sanitize_input($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    // Perform login using username and password
    public function login($username, $password)
    {
        $username = $this->sanitize_input($username);
        $password = $this->sanitize_input($password);

        // Perform server-side validation on input data
        if (empty($username) || empty($password)) {
            throw new Exception("Please enter both username and password.");
        }

        // Use prepared statements to prevent SQL injection
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the user exists and if the provided password matches the stored hashed password
        if ($user && password_verify($password, $user["password"])) {
            // Generate a random token
            $token = bin2hex(random_bytes(32));
			
			// Store user object in session
			$_SESSION["user"] = $user;
            // Store the token in the database along with the user ID and an expiration time (e.g., 1 hour from now)
            $expiryTime = time() + 3600; // 1 hour
            $stmt = $this->pdo->prepare("INSERT INTO user_tokens (user_id, token, expiry_time) VALUES (?, ?, ?)");
            $stmt->execute([$user["id"], $token, $expiryTime]);

            // Set the token as a cookie with HttpOnly and Secure flags
            setcookie("token", $token, $expiryTime, "/", "", true, true);

            return true;
        }

        return false;
    }

    // Check if the user is logged in with a valid token
    public function isLoggedIn()
    {
        if (isset($_COOKIE["token"])) {
            $token = $_COOKIE["token"];

            // Retrieve user token from the database
            $stmt = $this->pdo->prepare("SELECT * FROM user_tokens WHERE token = ? AND expiry_time > ?");
            $stmt->execute([$token, time()]);
            $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($tokenData) {
                // User is logged in with a valid token
                // Retrieve user data and set the session
                $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$tokenData["user_id"]]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["username"] = $user["username"];
                    return true;
                }
            }
        }

        return false;
    }

    // Logout the user by clearing the token and session data
    public function logout()
    {
        setcookie("token", "", time() - 3600, "/", "", true, true);
        session_unset();
        session_destroy();
    }
}

// Database connection configuration
$db_host = "localhost";
$db_name = "websec";
$db_user = "root";
$db_pass = "";
$errMessage = '';

try {
    // Connect to the database using PDO
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create a User object with the PDO instance
    $user = new User($pdo);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the user is attempting to log in
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    try {
        if ($user->login($username, $password)) {
            // Redirect the user to the dashboard or other authorized pages
            header("Location: dashboard.php");
            exit;
        } else {
			$errMessage = "Invalid username or password.";
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

// Check if the user is already logged in
if ($user->isLoggedIn()) {
    // Redirect the user to the dashboard or other authorized pages
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
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
    <form method="POST" action="">
		<section class="container">
			<h1>LOGIN</h1>
			<div class="form-element-container mb-12">
				<label for="username">Username:</label>
				<input type="text" name="username" required>
			</div>
			
			<div class="form-element-container mb-5">
				<label for="password">Password:</label>
				<input type="password" name="password" required>
			</div>
			<span class="errmsg mb-12"><?php echo $errMessage ?></span>
			<div class="btn-container">
				<a href="/register.php" class="mr-24">Register</a>
				<input type="submit" value="Login">
			</div>
			
		</section>
    </form>
</body>
</html>
